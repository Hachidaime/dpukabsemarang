{if $data.download ne true}
<div class="row mb-2">
  <div class="col-12 d-flex justify-content-end">
    <div class="dropdown">
      <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Export data">
        <i class="fas fa-download"></i>
      </button>
      <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
        <a class="dropdown-item" href="{$smarty.const.BASE_URL}/Laporan/dd1/pdf" target="_blank">PDF</a>
        <a class="dropdown-item" href="{$smarty.const.BASE_URL}/Laporan/dd1/xlsx" target="_blank">XLSX</a>
        <a class="dropdown-item" href="#" target="_blank">CSV</a>
      </div>
    </div>
  </div>
</div>
{/if}
{if $data.format eq 'xlsx'}
<div class="row">
  <div class="col">
    <h3 class="text-center">Data Dasar Prasarana Provinsi, Kabupaten/Kota</h3>
    <table>
      <tr>
        <td colspan="2">Provinsi</td>
        <td colspan="17">: Jawa Tengah</td>
        <td colspan="2">DD-1</td>
      </tr>
      <tr>
        <td colspan="2">Kabupaten</td>
        <td colspan="17">: Semarang</td>
        <td colspan="2"></td>
      </tr>
      <tr>
        <td colspan="2">Tahun</td>
        <td colspan="17">: 2020</td>
        <td colspan="2"></td>
      </tr>
      <tr>
        <td colspan="21">&nbsp;</td>
      </tr>
    </table>
  </div>
</div>
{elseif $data.format eq 'pdf'}
<div class="row" style="width: 100%">
  <div class="col">
    <h3 class="text-center">Data Dasar Prasarana Provinsi, Kabupaten/Kota</h3>
    <table width="100%">
      <tr>
        <td width="5%">Provinsi</td>
        <td width="85%">: Jawa Tengah</td>
        <td width="10%">
          <div class="bg-black text-white text-center">DD-1</div>
        </td>
      </tr>
      <tr>
        <td>Kabupaten</td>
        <td>: Semarang</td>
        <td></td>
      </tr>
      <tr>
        <td>Tahun</td>
        <td>: 2020</td>
        <td></td>
      </tr>
      <tr>
        <td colspan="3">&nbsp;</td>
      </tr>
    </table>
  </div>
</div>
{else}
<div class="row mb-2">
  <div class="col">
    <h3 class="text-center">Data Dasar Prasarana Provinsi, Kabupaten/Kota</h3>
    <div class="d-flex justify-content-between">
      <div style="width: 300px">
        <div class="row">
          <div class="col-3">Provinsi</div>
          <div class="col-6">: Jawa Tengah</div>
        </div>
        <div class="row">
          <div class="col-3">Kabupaten</div>
          <div class="col-6">: Semarang</div>
        </div>
        <div class="row">
          <div class="col-3">Tahun</div>
          <div class="col-6">: 2020</div>
        </div>
      </div>
      <div style="width: 100px;">
        <div class="bg-primary text-white text-center">DD-1</div>
      </div>
    </div>
  </div>
</div>
{/if}
<div class="row">
  <div class="col">
    <div class="table-responsive">
      <table class="table table-bordered table-sm" id="laporan-table">
        <thead>
          {foreach from=$data.thead key=row item=$myRow}
          <tr>
            {foreach from=$myRow key=col item=$myCol}
            {assign var=colData value=$myCol.data|replace:'data-':''}
            {assign var=colData value=$colData|replace:' halign':''}
            <th {$colData} class="text-center align-top">{$myCol.title}</th>
            {/foreach}
          </tr>
          {/foreach}
        </thead>
        <tbody>
          {foreach from=$data.data key=k item=$content}
          <tr>
            <td class="text-right">{$content.row}</td>
            <td class="text-center">{$content.no_jalan}</td>
            <td>{$content.nama_jalan}</td>
            <td></td>
            <td class="text-right">{$content.panjang_km|replace:'0.00':'-'}</td>
            <td class="text-right">{$content.lebar_rata|replace:'0.00':'-'}</td>
            <td class="text-right">{$content.perkerasan_1|replace:'0.00':'-'}</td>
            <td class="text-right">{$content.perkerasan_2|replace:'0.00':'-'}</td>
            <td class="text-right">{$content.perkerasan_3|replace:'0.00':'-'}</td>
            <td class="text-right">{$content.perkerasan_4|replace:'0.00':'-'}</td>
            <td class="text-right">{$content.kondisi_1|replace:'0.00':'-'}</td>
            <td class="text-right">{$content.kondisi_1_percent|replace:'0.00':'-'}</td>
            <td class="text-right">{$content.kondisi_2|replace:'0.00':'-'}</td>
            <td class="text-right">{$content.kondisi_2_percent|replace:'0.00':'-'}</td>
            <td class="text-right">{$content.kondisi_3|replace:'0.00':'-'}</td>
            <td class="text-right">{$content.kondisi_3_percent|replace:'0.00':'-'}</td>
            <td class="text-right">{$content.kondisi_4|replace:'0.00':'-'}</td>
            <td class="text-right">{$content.kondisi_4_percent|replace:'0.00':'-'}</td>
            <td>{$content.lhr}</td>
            <td>{$content.npk}</td>
            <td>{$content.keterangan}</td>
          </tr>
          {/foreach}
          <tr>
            <td colspan="4">A. Total Panjang Jalan (km)</td>
            <td class="text-right">{$data.panjang.jalan}</td>
            <td></td>
            {foreach from=$data.panjang.perkerasan key=k item=$perkerasan}
            <td class="text-right">{$perkerasan}</td>
            {/foreach}
            {foreach from=$data.panjang.kondisi key=k item=$kondisi}
            <td class="text-right">{$kondisi}</td>
            <td></td>
            {/foreach}
            <td></td>
            <td></td>
            <td></td>
          </tr>
          <tr>
            <td colspan="4">B. Persentase Kondisi Jalan (%)</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            {foreach from=$data.panjang.kondisi_percent key=k item=$kondisi_percent}
            <td></td>
            <td class="text-right">{$kondisi_percent}</td>
            {/foreach}
            <td></td>
            <td></td>
            <td></td>
          </tr>
          <tr>
            <td colspan="10">C. Persentase Jalan Mantap (%)</td>
            <td colspan="4" class="text-center">{$data.panjang.mantap}</td>
            <td colspan="4"></td>
            <td colspan="3"></td>
          </tr>
          <tr>
            <td colspan="14">D. Persentase Jalan Tidak Mantap (%)</td>
            <td colspan="4" class="text-center">{$data.panjang.tidak_mantap}</td>
            <td colspan="3"></td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>