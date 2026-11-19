<?php

namespace App\Http\Controllers;

use App\Models\Income;
use App\Models\TaxInvoice;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TaxInvoiceController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'income_id' => 'nullable|exists:incomes,id',
            'vendor_id' => 'nullable|exists:vendors,id',
            'faktur_number' => 'required|string|max:80|unique:tax_invoices,faktur_number',
            'transaction_code' => 'required|string|max:10',
            'tax_period' => 'required|string|max:20',
            'faktur_date' => 'required|date',
            'buyer_name' => 'required|string|max:255',
            'buyer_npwp' => 'nullable|string|max:50',
            'buyer_nitku' => 'nullable|string|max:50',
            'buyer_address' => 'nullable|string',
            'dpp_amount' => 'required|numeric|min:0',
            'ppn_rate' => 'nullable|numeric|min:0|max:100',
            'pph_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $ppnRate = (float) ($validated['ppn_rate'] ?? 11.00);
        $dpp = (float) $validated['dpp_amount'];
        $ppnAmount = round($dpp * ($ppnRate / 100), 2);

        if (empty($validated['vendor_id']) && ! empty($validated['income_id'])) {
            $validated['vendor_id'] = Income::where('id', $validated['income_id'])->value('vendor_id');
        }

        TaxInvoice::create(array_merge($validated, [
            'ppn_rate' => $ppnRate,
            'ppn_amount' => $ppnAmount,
            'pph_amount' => $validated['pph_amount'] ?? round($dpp * 0.02, 2),
            'coretax_status' => 'draft',
        ]));

        return redirect()->route('financial.index', ['tab' => 'tax'])->with('success', 'Faktur Pajak berhasil dibuat dan siap diekspor ke Coretax!');
    }

    public function exportCoretaxXml(TaxInvoice $taxInvoice): Response
    {
        $taxInvoice->update(['coretax_status' => 'exported']);

        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><TaxInvoiceBulk></TaxInvoiceBulk>');
        $xml->addChild('TIN', '0123456789012000'); // NPWP Koperasi Swadharma
        $list = $xml->addChild('ListOfTaxInvoice');
        $item = $list->addChild('TaxInvoice');
        $item->addChild('TaxInvoiceDate', $taxInvoice->faktur_date->format('Y-m-d'));
        $item->addChild('TaxInvoiceOpt', 'Normal');
        $item->addChild('TrxCode', $taxInvoice->transaction_code);
        $item->addChild('FakturNumber', $taxInvoice->faktur_number);
        $item->addChild('TaxPeriod', $taxInvoice->tax_period);
        $item->addChild('BuyerName', htmlspecialchars($taxInvoice->buyer_name));
        $item->addChild('BuyerTIN', $taxInvoice->buyer_npwp ?: '0000000000000000');
        $item->addChild('BuyerIDTKU', $taxInvoice->buyer_nitku ?: '0000000000000000000000');
        $item->addChild('TaxBase', number_format((float) $taxInvoice->dpp_amount, 2, '.', ''));
        $item->addChild('VAT', number_format((float) $taxInvoice->ppn_amount, 2, '.', ''));
        $item->addChild('STLG', '0.00');

        $filename = 'Coretax_Faktur_'.preg_replace('/[^A-Za-z0-9_-]/', '_', $taxInvoice->faktur_number).'.xml';

        return response($xml->asXML(), 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    public function destroy(TaxInvoice $taxInvoice)
    {
        $taxInvoice->delete();

        return redirect()->route('financial.index', ['tab' => 'tax'])->with('success', 'Faktur pajak berhasil dihapus.');
    }
}
