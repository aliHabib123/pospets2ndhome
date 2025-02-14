<?php

namespace App\Services;

use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\PrintConnectors\DummyPrintConnector;
use Mike42\Escpos\PrintConnectors\CupsPrintConnector;
use Mike42\Escpos\EscposImage;
use Mike42\Escpos\Printer;
use App\Sale;
use App\Setting;
use Illuminate\Support\Facades\Session;

class PrinterService
{
    protected $printer;
    protected $connector;
    protected $isDummy = false;

    public function __construct()
    {
        try {
            $os = php_uname('s');
            $printerName = Setting::where('config', 'printer_name')->first()->value ?? 'POS-58';
            
            if (stripos($os, 'Windows') !== false) {
                $this->connector = new WindowsPrintConnector($printerName);
            } elseif (stripos($os, 'Darwin') !== false) {
                $this->connector = new CupsPrintConnector($printerName);
            } elseif (stripos($os, 'Linux') !== false) {
                $this->connector = new FilePrintConnector("/dev/usb/lp0");
            } else {
                $this->useDummyPrinter("Unsupported OS: " . $os);
            }
            
            //$this->printer = new Printer($this->connector);
        } catch (\Exception $e) {
            $this->useDummyPrinter("Printer connection failed: " . $e->getMessage());
        }
        //$this->useDummyPrinter("Printer connection failed: ");
    }

    protected function useDummyPrinter($reason)
    {
        \Log::info("Using dummy printer - " . $reason);
        $this->connector = new DummyPrintConnector();
        $this->printer = new Printer($this->connector);
        $this->isDummy = true;
    }

    /**
     * Print the logo on the receipt
     */
    private function printLogo()
    {
        $logoPath = public_path('images/output.png');
        if (file_exists($logoPath)) {
            //usleep(500000);
            $this->printer->feed();
            $logo = EscposImage::load($logoPath, false);
            $this->printer->setJustification(Printer::JUSTIFY_CENTER);
            $this->printer->bitImage($logo);
            $this->printer->feed();
        }
    }

    public function printReceipt(Sale $sale)
    {
        try {
            //$this->printer->initialize();
            $this->printer = new Printer($this->connector);
            // Print logo if exists
            $this->printLogo();

            // Build receipt content
            $this->buildReceiptContent($sale);
            
            $this->printer->cut();

            // If using dummy printer, save to file before closing
            if ($this->isDummy) {
                $this->saveReceiptToFile($sale->id, '');
            }

            
            $this->printer->close();
            $this->printer->pulse();
            
            return true;
        } catch (\Exception $e) {
            \Log::error("Printing failed: " . $e->getMessage());
            return false;
        }
    }

    protected function buildReceiptContent(Sale $sale)
    {
        // Header
        $companyName = Setting::where('config', 'company_name')->first()->value ?? 'Company Name';
        $selectedLocationId = Session::get('selectedLocationId');
        $address = config('locations.locations')[$selectedLocationId] ?? 'Address';
        $phone = Setting::where('config', 'phone')->first()->value ?? 'Phone';
        
        $this->printer->text(str_repeat("=", 40) . "\n");
        $this->printer->text(str_pad($companyName, 40, " ", STR_PAD_BOTH) . "\n");
        $this->printer->text(str_pad($address, 40, " ", STR_PAD_BOTH) . "\n");
        $this->printer->text(str_pad("Tel: " . $phone, 40, " ", STR_PAD_BOTH) . "\n");
        $this->printer->text(str_repeat("=", 40) . "\n\n");

        // Sale info
        //$this->printer->setJustification(Printer::JUSTIFY_LEFT);
        $this->printer->text(str_pad("Invoice #: " . $sale->id , 40, " ", STR_PAD_RIGHT) . "\n");
        $this->printer->text(str_pad("Date: " . $sale->created_at , 40, " ", STR_PAD_RIGHT) . "\n");
        $this->printer->text(str_pad("Customer: " . ($sale->customer ? $sale->customer->name : 'Walk-in') , 40, " ", STR_PAD_RIGHT) . "\n");
        $this->printer->setJustification(Printer::JUSTIFY_CENTER);
        $this->printer->text(str_repeat("-", 40) . "\n\n");

        // Calculate totals
        $subtotal = 0;

        // Items
        foreach ($sale->saleItems as $index => $item) {
            $itemName = $item->item ? $item->item->item_name : 'Unknown Item';
            
            // Calculate item total
            $itemTotal = $item->quantity * $item->selling_price;
            $subtotal += $itemTotal;
            
            // Format prices without decimals and add LBP
            $unitPrice = number_format($item->selling_price, 0) . " LBP";
            $total = number_format($itemTotal, 0) . " LBP";
            //$this->printer->setJustification(Printer::JUSTIFY_LEFT);
            // Item name and quantity on first line (left aligned)
            $this->printer->text(str_pad(($index + 1) . ". " . $itemName . " - QTY " . $item->quantity , 40, " ", STR_PAD_RIGHT) . "\n");
            
            // Price on second line (left aligned)
            $this->printer->text(str_pad("Price: " . $unitPrice , 40, " ", STR_PAD_RIGHT) . "\n");
            
            // Total on third line (left aligned)
            $this->printer->text(str_pad("Total: " . $total , 40, " ", STR_PAD_RIGHT) . "\n\n");
        }
        $this->printer->setJustification(Printer::JUSTIFY_CENTER);
        $this->printer->text(str_repeat("-", 40) . "\n");

        // Calculate discount
        $discountAmount = 0;
        if ($sale->discount && $sale->discount > 0) {
            $discountAmount = floatval($sale->discount);
        }
        
        $grandTotal = $subtotal - $discountAmount;

        // Totals section aligned to the right
        $this->printer->text(str_pad("Subtotal: " . number_format($subtotal, 0) . " LBP", 40, " ", STR_PAD_LEFT) . "\n");
        
        if ($sale->discount && $sale->discount > 0) {
            $this->printer->text(str_pad(
                "Discount: -" . number_format($discountAmount, 0) . " LBP", 
                40, 
                " ", 
                STR_PAD_LEFT
            ) . "\n");
        }
        
        $this->printer->text(str_pad("Grand Total: " . number_format($grandTotal, 0) . " LBP", 40, " ", STR_PAD_LEFT) . "\n");
        $this->printer->text(str_repeat("=", 40) . "\n");

        $this->printer->feed();
        $this->printer->text("Thank you for your visit!\n");
        $this->printer->setJustification(Printer::JUSTIFY_CENTER);
        $this->printer->feed();
        $this->printer->text("Follow us on Instagram: @pets2ndhomelb\n");
        $this->printer->feed();
    }

    protected function saveReceiptToFile($saleId, $content)
    {
        $directory = storage_path('app/receipts');
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }
        
        $filename = $directory . '/receipt_' . $saleId . '_' . date('Y-m-d_H-i-s') . '.txt';
        file_put_contents($filename, $content);
        \Log::info("Receipt saved to file: " . $filename);
    }

    public function testPrinter()
    {
        $this->isDummy = true;

        try {
            $this->printer = new Printer($this->connector);
            $this->printer->feed(2);
            // Print logo if exists
            $this->printLogo();        

            $testReceipt = str_repeat("=", 40) . "\n";
            $testReceipt .= str_pad("TEST RECEIPT", 40, " ", STR_PAD_BOTH) . "\n";
            $testReceipt .= str_pad(date('Y-m-d H:i:s'), 40, " ", STR_PAD_BOTH) . "\n";
            $testReceipt .= str_repeat("=", 40) . "\n";

            $this->printer->text($testReceipt);
            $this->printer->feed(2);

            $this->printer->cut();

            // If using dummy printer, save to file before closing
            if ($this->isDummy) {
                $this->saveReceiptToFile('test', $testReceipt);
            }

            $this->printer->close();

            if ($this->isDummy) {
                return "Test receipt saved to file. Check storage/app/receipts/";
            }

            return "Test receipt printed successfully";
        } catch (\Exception $e) {
            return "Printer test failed: " . $e->getMessage();
        }
    }
}
