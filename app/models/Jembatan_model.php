<?php

/**
 * * app/models/Jembatan_model.php
 */
class Jembatan_model extends Database
{
  /**
   * * Define variable
   */
  private $my_tables = ['jembatan' => 'tjembatan'];

  /**
   * * Jembatan_model::getTable
   * ? Get table name
   * @param string $type
   * ? Type
   */
  public function getTable(string $type = null)
  {
    return Functions::getTable($this->my_tables, $type);
  }

  /**
   * * Jembatan_model::getJembatanForm
   * ? Jembatan form
   */
  public function getJembatanForm()
  {
    Functions::setDataSession('form', ['hidden', 'id', 'id', '', []]);
    Functions::setDataSession('form', ['select', 'no_jalan', 'no_jalan', 'Ruas Jalan', $this->model('Jalan_model')->getJalanOptions(), true, false]);
    Functions::setDataSession('form', ['text', 'no_jembatan', 'no_jembatan', 'Nomor Jembatan', [], true, true]);
    Functions::setDataSession('form', ['text', 'nama_jembatan', 'nama_jembatan', 'Nama Jembatan', [], true, false]);
    Functions::setDataSession('form', ['text', 'nama_sungai', 'nama_sungai', 'Nama Sungai', [], true, false]);
    Functions::setDataSession('form', ['number', 'lebar', 'lebar', 'Lebar (m)', [], true, false]);
    Functions::setDataSession('form', ['number', 'panjang', 'panjang', 'Panjang (m)', [], true, false]);
    Functions::setDataSession('form', ['number', 'latitude', 'latitude', 'Latitude', [], true, false]);
    Functions::setDataSession('form', ['number', 'longitude', 'longitude', 'Longitude', [], true, false]);
    Functions::setDataSession('form', ['number', 'bentang', 'bentang', 'Jumlah Bentang', [], false, false]);
    Functions::setDataSession('form', ['textarea', 'keterangan', 'keterangan', 'Keterangan', [], false, false]);
    Functions::setDataSession('form', ['text', 'tipe_bangunan_atas', 'tipe_bangunan_atas', 'Tipe Bangunan Atas', [], false, false]);
    Functions::setDataSession('form', ['select', 'kondisi_bangunan_atas', 'kondisi_bangunan_atas', 'Kondisi Bangunan Atas', $this->options('kondisi_opt'), false, false]);
    Functions::setDataSession('form', ['img', 'foto_bangunan_atas', 'foto_bangunan_atas', 'Foto Bangunan Atas', [], false, false]);
    Functions::setDataSession('form', ['text', 'tipe_bangunan_bawah', 'tipe_bangunan_bawah', 'Tipe Bangunan Bawah', [], false, false]);
    Functions::setDataSession('form', ['select', 'kondisi_bangunan_bawah', 'kondisi_bangunan_bawah', 'Kondisi Bangunan Bawah', $this->options('kondisi_opt'), false, false]);
    Functions::setDataSession('form', ['img', 'foto_bangunan_bawah', 'foto_bangunan_bawah', 'Foto Bangunan Bawah', [], false, false]);
    Functions::setDataSession('form', ['text', 'tipe_fondasi', 'tipe_fondasi', 'Tipe Fondasi', [], false, false]);
    Functions::setDataSession('form', ['select', 'kondisi_fondasi', 'kondisi_fondasi', 'Kondisi Fondasi', $this->options('kondisi_opt'), false, false]);
    Functions::setDataSession('form', ['img', 'foto_fondasi', 'foto_fondasi', 'Foto Fondasi', [], false, false]);
    Functions::setDataSession('form', ['text', 'tipe_lantai', 'tipe_lantai', 'Tipe Lantai', [], false, false]);
    Functions::setDataSession('form', ['select', 'kondisi_lantai', 'kondisi_lantai', 'Kondisi Lantai', $this->options('kondisi_opt'), false, false]);
    Functions::setDataSession('form', ['img', 'foto_lantai', 'foto_lantai', 'Foto Lantai', [], false, false]);

    return Functions::getDataSession('form');
  }

  /**
   * * Jembatan_model::getJembatanThead
   * ? Jembatan table column list
   */
  public function getJembatanThead()
  {
    // TODO: Set column table
    Functions::setDataSession('thead', ['0', 'row', '#']);
    Functions::setDataSession('thead', ['0', 'no_jembatan', 'Nomor Jembatan', 'data-halign="center" data-align="center" data-width="200"']);
    Functions::setDataSession('thead', ['0', 'nama_jembatan', 'Nama Jembatan', 'data-halign="center" data-align="left" data-width="250"']);
    Functions::setDataSession('thead', ['0', 'jalan', 'Ruas Jalan', 'data-halign="center" data-align="left" data-width="250"']);
    Functions::setDataSession('thead', ['0', 'keterangan', 'Keteranan', 'data-halign="center" data-align="left"']);
    Functions::setDataSession('thead', ['0', 'operate']);
    return Functions::getDataSession('thead');
  }

  /**
   * * Jembatan_model::getJembatan
   * ? Get data from database
   */
  public function getJembatan()
  {
    $params = [];
    $search = Functions::getSearch();

    if (!empty($search['search'])) $params['filter'] = "nama_jembatan LIKE '%{$search['search']}%'";
    if (isset($search['limit'])) $params['limit'] = $search['limit'];
    if (isset($search['offset'])) $params['offset'] = $search['offset'];

    $params['sort'] = "{$this->my_tables['jembatan']}.no_jembatan ASC";

    $query = $this->getSelectQuery($this->my_tables['jembatan'], $params);

    $this->execute($query);
    // var_dump($this->db);
    return $this->multiarray();
  }

  /**
   * * Jembatan_model::totalJembatan
   * ? Get total rows in database
   */
  public function totalJembatan()
  {
    return $this->totalRows($this->my_tables['jembatan']);
  }

  /**
   * * Jembatan_model::getJembatanDetail
   * ? Get Jembatan detail
   * @param int $id
   * ? Jembatan ID
   */
  public function getJembatanDetail(int $id)
  {
    $params = [];
    $params['filter'] = "id = ?";
    $query = $this->getSelectQuery($this->my_tables['jembatan'], $params);
    $bindVar = [$id];

    $this->execute($query, $bindVar);
    return $this->singlearray();
  }

  /**
   * * Jembatan_model::prepareSaveJembatan
   * ? Preparing data to save into database
   */
  public function prepareSaveJembatan()
  {
    $values = [];
    $bindVar = [];
    foreach ($_POST as $key => $value) {
      if ($key == 'id') continue;
      array_push($values, "{$key}=?");
      array_push($bindVar, $value);
    }
    $values = implode(", ", $values);
    $values .= ", login_id = ?, remote_ip = ?";

    array_push($bindVar, Auth::User('id'), $_SERVER['REMOTE_ADDR']);

    return [$values, $bindVar];
  }

  /**
   * * Jembatan_model::createJembatan
   * ? Insert new Jembatan
   */
  public function createJembatan()
  {
    list($values, $bindVar) = $this->prepareSaveJembatan();

    $query = "INSERT INTO {$this->my_tables['jembatan']} SET {$values}, update_dt = NOW()";

    $this->execute($query, $bindVar);
    return $this->affected_rows();
  }

  /**
   * * Jembatan_model::updateJembatan
   * ? Update existing Jembatan
   */
  public function updateJembatan()
  {
    list($values, $bindVar) = $this->prepareSaveJembatan();
    array_push($bindVar, $_POST['id']);

    $query = "UPDATE {$this->my_tables['jembatan']} SET {$values}, update_dt = NOW() WHERE id=?";
    $this->execute($query, $bindVar);
    return $this->affected_rows();
  }

  /**
   * * Jembatan_model::deleteJembatan
   * ? Remove Jembatan from database
   */
  public function deleteJembatan(int $id)
  {
    $query = "DELETE FROM {$this->my_tables['jembatan']} WHERE id = ?";
    $bindVar = [$id];
    $this->execute($query, $bindVar);
    return $this->affected_rows();
  }
}
