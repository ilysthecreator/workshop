<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        /*
         * TnJ Self-Adhesive Label Paper No. 108
         * ─────────────────────────────────────
         * Label size  : 38mm × 18mm
         * Grid        : 5 cols × 8 rows = 40 labels per sheet
         * Gap         : 2mm between each label
         * Page size   : 222mm × 175mm  (user-confirmed)
         * @page margin: 3mm all sides
         * Usable area : 216mm × 169mm
         *
         * x_start : 1–5  (column, 1-indexed)
         * y_start : 1–8  (row,    1-indexed)
         */

        @page {
            size: 222mm 185mm;
            /* margin: 3mm; */
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            background: #ffffff;
            font-family: Arial, sans-serif;
        }

        /* One page wrapper — fits exactly inside @page usable area */
        .page {
            width: 220mm;   /* 222mm − 2×3mm @page margin */
            height: 175mm;  /* 175mm − 2×3mm @page margin */
            background: #f3e280;
            page-break-after: always;
            padding: 3mm;
        }

        /* Blade $loop->last adds this class — prevents blank trailing page */
        .page-last {
            page-break-after: auto;
        }

        /* Table-based grid — DomPDF safe (no flexbox/grid) */
        .label-table {
            border-collapse: separate;
            border-spacing: 0;
            table-layout: fixed;
        }

        .label-cell {
            width: 38mm;
            height: 18mm;
            padding: 0;
            vertical-align: top;
        }

        .col-gap {
            width: 2mm;
        }

        .row-gap {
            height: 2mm;
        }

        /* White sticker label */
        .label-box {
            width: 35mm;
            height: 16mm;
            background: #ffffff;
            border: 0.4mm dashed #aaaaaa;
            padding: 1.2mm 1.5mm;
            position: relative;
            overflow: hidden;
        }

        /* Empty slot — shows yellow backing paper */
        .label-empty {
            width: 38mm;
            height: 18mm;
            background: transparent;
            border: dotted #111111 2px;
        }

        .label-nama {
            font-size: 5.5pt;
            font-weight: bold;
            color: #111111;
            line-height: 1.2;
            overflow: hidden;
            white-space: nowrap;
        }

        .label-harga {
            font-size: 7.5pt;
            font-weight: bold;
            color: #111111;
            margin-top: 0.8mm;
            line-height: 1;
        }

        .label-timestamp {
            font-size: 4pt;
            color: #666666;
            margin-top: 0.6mm;
            line-height: 1.2;
        }

        .label-id {
            position: absolute;
            bottom: 1mm;
            right: 1.5mm;
            font-size: 4pt;
            color: #aaaaaa;
        }
    </style>
</head>
<body>

@php
    /*
     * Grid constants
     */
    $cols    = 5;
    $rows    = 8;
    $perPage = $cols * $rows; // 40

    // Use skipCount passed from controller or calculate it
    $startOffset = isset($skipCount) ? (int)$skipCount : 0;

    /*
     * Build flat slot array.
     * null  = empty slot (already-used label / start offset padding)
     * array = item data
     */
    $itemsArray = isset($barangs) ? $barangs->toArray() : [];

    $slots = array_merge(
        array_fill(0, $startOffset, null),
        $itemsArray
    );

    // Pad to fill the last page completely (avoids layout gaps)
    $remainder = count($slots) % $perPage;
    if ($remainder !== 0) {
        $slots = array_merge($slots, array_fill(0, $perPage - $remainder, null));
    }

    // Split into pages of 40 slots each
    $pages = array_chunk($slots, $perPage);
@endphp

@foreach ($pages as $pageSlots)
    {{--
        $loop->last → adds .page-last → page-break-after: auto
        This prevents DomPDF from rendering a blank trailing page.
    --}}
    <div class="page {{ $loop->last ? 'page-last' : '' }}">
        <table class="label-table">
            @php
                $pageRows = array_chunk($pageSlots, $cols);
            @endphp

            @foreach ($pageRows as $rowIndex => $rowSlots)

                {{-- 2mm gap row between label rows (not before the first row) --}}
                @if ($rowIndex > 0)
                    <tr>
                        @for ($g = 0; $g < ($cols * 2 - 1); $g++)
                            <td class="row-gap"></td>
                        @endfor
                    </tr>
                @endif

                <tr>
                    @foreach ($rowSlots as $colIndex => $slot)

                        {{-- 2mm gap column between label columns (not before the first col) --}}
                        @if ($colIndex > 0)
                            <td class="col-gap"></td>
                        @endif

                        <td class="label-cell">
                            @if ($slot !== null)
                                <div class="label-box">
                                    <div class="label-nama">{{ $slot['nama'] }}</div>
                                    <div class="label-harga">Rp {{ number_format($slot['harga'], 0, ',', '.') }}</div>
                                    <div class="label-timestamp">{{ $slot['timestamp'] }}</div>
                                    <div class="label-id">{{ $slot['id_barang'] }}</div>
                                </div>
                            @else
                                <div class="label-empty"></div>
                            @endif
                        </td>

                    @endforeach
                </tr>

            @endforeach
        </table>
    </div>
@endforeach

</body>
</html>