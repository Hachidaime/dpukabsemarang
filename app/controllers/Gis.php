<?php

/**
 * * app/controllers/Gis.php
 */
class Gis extends Controller
{
  private $my_model;
  private $jalan_options;
  private $no_jalan;

  /**
   * * Gis::index()
   * ? Menampilkan Halaman Gis
   */
  public function index(string $param1 = null, string $param2 = null)
  {
    $this->my_model = $this->model('Gis_model');
    $this->no_jalan = $param2;

    switch ($param1) {
      case 'jalan':
        $this->SearchJalan();
        break;
      case 'datajalan':
        $this->DataJalan();
        break;
      case 'getgeo':
        $this->GetGeo();
        break;
      default:
        $this->GisDefault();
        break;
    }
  }

  private function GisDefault()
  {
    Functions::setTitle('SIG (Sistem Informasi Geografi)');
    $data = [];
    $data['searchform'] = $this->dofetch('Layout/Form', [
      'formClass' => "searchGisForm",
      'form' => $this->my_model->getGisForm(),
      'mini' => true
    ]);
    $this->view('Gis/index', $data);
  }

  private function SearchJalan()
  {
    $cond = [];
    if (!empty($_POST['kepemilikan'])) {
      if ($_POST['kepemilikan'] != 'all') {
        $cond[] = "kepemilikan = {$_POST['kepemilikan']}";
      }
      $jalan_options = $this->model('Jalan_model')->getJalanOptions($cond);

      echo json_encode($jalan_options);
      exit;
    }
  }

  private function DataJalan()
  {
    if ($this->no_jalan == 'semua') {
      $this->DataJalanSemua();
      exit;
    }

    if ($this->no_jalan > 0) {
      $result = [];
      $kepemilikan_opt = $this->options('kepemilikan_opt'); // TODO: Get Kepemilikan Options

      // * Setup from database
      $cond[] = "jenis = 1"; // ? Setup for jalan
      // TODO: Get setup from database
      list($setup_jalan,) = $this->model('Setup_model')->getSetup($cond);

      // TODO: Formating setup as JSON
      list($style, $lineStyle, $iconStyle) = Functions::getStyle($setup_jalan);

      list($jalan, $detail, $jembatan) = $this->my_model->getDetailJalan($this->no_jalan);

      $jalan['kepemilikan_text'] = $kepemilikan_opt[$jalan['kepemilikan']];
      $jalan['lebar'] = $jalan['lebar_rata'];

      $coord = $jalan['segmented'];
      if (empty($coord)) $coord = $jalan['ori'];

      unset($jalan['ori']);
      unset($jalan['segmented']);

      $koordinat = [];
      foreach (json_decode($coord, true) as $value) {
        $koordinat[] = Functions::makeMapPoint($value, true);
      }

      $jalan['koordinat'] = $koordinat;
      $jalan['style'] = $style[$jalan['kepemilikan']][0][0];

      list($segment, $complete, $perkerasan, $kondisi, $awal, $akhir) = Functions::getLineFromDetail($detail, $lineStyle, $iconStyle);
      $jembatan = Functions::getPointFromJembatan($jembatan, $iconStyle);
      // var_dump($segment);

      $position = [
        'koordinat' => $this->GetGeo()
      ];

      $result = [
        'jalan'         => Functions::createFeature($style, $jalan, 1),
        'segment'       => Functions::createFeatureCollection($style, $segment, 2),
        'complete'      => Functions::createFeatureCollection($style, $complete, 1),
        'perkerasan'    => Functions::createFeatureCollection($style, $perkerasan, 1),
        'kondisi'       => Functions::createFeatureCollection($style, $kondisi, 1),
        'awal'          => Functions::createFeatureCollection($style, $awal, 2),
        'akhir'         => Functions::createFeatureCollection($style, $akhir, 2),
        'jembatan'      => Functions::createFeatureCollection($style, $jembatan, 2),
        'position'      => Functions::createFeature($style, $position, 2)
      ];
      echo json_encode($result);
      exit;
    }
  }

  private function DataJalanSemua()
  {
    $filedir = DOC_ROOT . "data/";

    $data = ['jalan', 'segment', 'complete', 'perkerasan', 'kondisi', 'awal', 'akhir', 'jembatan'];

    $result = [];
    foreach ($data as $value) {
      $name = ucfirst($value);
      $filename = "{$name}.json";
      $filepath = "{$filedir}{$filename}";
      $result[$value] = [];
      if (file_exists($filepath)) {
        $result[$value] = Functions::readJSON($filepath);
      }
    }
    echo json_encode($result);
    exit;
  }

  private function GetGeo()
  {
    $remote_ip = $_SERVER['REMOTE_ADDR'];
    $geo = Functions::getGeo($remote_ip);
    return $geo;
  }
}
