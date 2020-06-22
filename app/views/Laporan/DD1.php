<div class="table-responsive">
  <table class="table table-bordered table-sm">
    <thead>
      {foreach from=$data.thead key=row item=$myRow}
      <tr>
        {foreach from=$myRow key=col item=$myCol}
        {assign var=colData value=$myCol.data|replace:'data-':''}
        {assign var=colData value=$colData|replace:' halign':''}
        <th {$colData} class="align-top text-center">{$myCol.title}</th>
        {/foreach}
      </tr>
      {/foreach}
    </thead>
    <tbody>
      {foreach from=$data.data key=k item=$content}
      <tr>
        <td>{$content.row}</td>
        <td>{$content.no_jalan}</td>
        <td>{$content.nama_jalan}</td>
        <td></td>
        <td class="text-right">{$content.panjang_km}</td>
        <td class="text-right">{$content.lebar_rata}</td>
        <td class="text-right">{$content.perkerasan_1}</td>
        <td class="text-right">{$content.perkerasan_2}</td>
        <td class="text-right">{$content.perkerasan_3}</td>
        <td class="text-right">{$content.perkerasan_4}</td>
        <td class="text-right">{$content.kondisi_1}</td>
        <td class="text-right">{$content.kondisi_1_percent}</td>
        <td class="text-right">{$content.kondisi_2}</td>
        <td class="text-right">{$content.kondisi_2_percent}</td>
        <td class="text-right">{$content.kondisi_3}</td>
        <td class="text-right">{$content.kondisi_3_percent}</td>
        <td class="text-right">{$content.kondisi_4}</td>
        <td class="text-right">{$content.kondisi_4_percent}</td>
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