<?php

/**
 * * app/controllers/Jalan.php
 */
class Jalan extends Controller
{
    private $old_detail;
    private $no_jalan;
    /**
     * * Start Jalan 
     */
    /**
     * * Jalan::index
     * ? Main method
     */
    public function index(string $param1 = null, string $param2 = null)
    {
        $this->my_model = $this->model('Jalan_model');

        switch ($param1) {
            case 'search':
                $this->JalanSearch();
                break;
            case 'add':
                $this->JalanAdd();
                break;
            case 'edit':
                if (!isset($param2)) Header("Location: " . BASE_URL . "/StaticPage/Error404");
                $this->JalanEdit($param2);
                break;
            case 'submit':
                $this->JalanSubmit();
                break;
            case 'remove':
                $this->JalanRemove();
                break;
            case 'generate':
                $this->model('Data_model')->generateData();
                break;
            default:
                $this->JalanDefault();
        }
    }

    /**
     * * Jalan::JalanDefaut
     * TODO: Lowad Jalan List
     */
    private function JalanDefault()
    {
        // TODO: Clear coordinates session
        Functions::clearDataSession('coordinates');

        // TODO: Set title
        Functions::setTitle("Jalan");

        // TODO: Load toolbar: add button
        $data['toolbar'][] = $this->dofetch('Component/Button', $this->btn_add);

        // TODO: Load table properties: column name, data-url
        $data['data'] = Functions::defaultTableData();
        $data['thead'] = $this->my_model->getJalanThead();
        $data['url'] = BASE_URL . "/Jalan/index/search";

        // TODO: Load table template
        $table = $this->dofetch('Layout/Table', $data);

        // TODO: Load template
        $data['main'][] = $table;
        $this->view('Layout/Default', $data);
    }

    /**
     * * Jalan::JalanSearch
     * TODO: Search Jalan list
     */
    private function JalanSearch()
    {
        // TODO: Search Jalan on database: list & total
        list($list, $count) = $this->my_model->getJalan();
        $total = $this->my_model->totalJalan();

        // TODO: Load Kepemilikan Options
        $kepemilikan_opt = $this->options('kepemilikan_opt');

        // TODO: Prepare data to load on template
        $rows = [];
        foreach ($list as $idx => $row) {
            $row['kepemilikan'] = $kepemilikan_opt[$row['kepemilikan']];
            $row['row'] = Functions::getSearch()['offset'] + $idx + 1;
            array_push($rows, $row);
        }

        // TODO: Echoing data as JSON
        Functions::setDataTable($rows, $count, $total);
        exit;
    }

    private function JalanForm($data)
    {
        $data['form'] = $this->my_model->getJalanForm();

        $data['toolbar'][] = $this->dofetch('Component/Button', Functions::makeButton('button', 'genCoord', '<i class="fas fa-route"></i>&nbsp;Generate Koordinat', 'warning', 'btn-gen-coord', 250));
        $data['main'][] = $this->dofetch('Layout/Form', $data);

        $data['data'] = Functions::defaultTableData();
        $data['thead'] = $this->my_model->getKoordinatThead();
        $data['search'] = 'false';

        $data['main'][] = $this->dofetch('Layout/Table', $data);

        // TODO: Load Koordinat Form
        unset($data['form']);
        $data['formClass'] = 'koordinatForm';
        $data['form'] = $this->my_model->getKoordinatForm();
        $data['modalbody'][] = $this->dofetch('Layout/Form', $data);

        $data['modalfoot'][] = $this->dofetch('Component/Button', Functions::makeButton('button', 'cancel-koordinat', 'Cancel', 'danger', 'btn-cancel-koordinat'));
        $data['modalfoot'][] = $this->dofetch('Component/Button', Functions::makeButton('button', 'submit-koordinat', 'Submit', 'success', 'btn-submit-koordinat'));
        $data['modalId'] = 'koordinatModal';

        $this->form($data);
    }

    /**
     * TODO: Menampilkan Form Tambah Jalan
     */
    private function JalanAdd()
    {
        Functions::clearDataSession('coordinates');
        Functions::setTitle("Tambah Jalan");

        $data['url'] = BASE_URL . "/Jalan/Koordinat/search";
        $this->JalanForm($data);
    }

    private function JalanEdit($id)
    {
        Functions::clearDataSession('coordinates');
        Functions::setTitle("Edit Jalan");

        list($detail, $count) = $this->JalanDetail($id);
        if ($count <= 0) Header("Location: " . BASE_URL . "/StaticPage/Error404");
        $data['detail'] = $detail;

        $data['url'] = BASE_URL . "/Jalan/Koordinat/search/{$detail['no_jalan']}";
        $this->JalanForm($data);
    }

    private function JalanSubmit()
    {
        $error = $this->JalanValidate();

        if (!$error) {
            echo json_encode($this->JalanProcess());
        } else {
            echo json_encode($error);
        }
        exit;
    }

    private function JalanValidate()
    {
        $form = $this->my_model->getJalanForm();
        foreach ($form as $row) {
            $this->validate($_POST, $row, 'Jalan_model', 'jalan');
        }

        return Functions::getDataSession('alert');
    }

    private function JalanProcess()
    {
        $form = $this->my_model->getJalanForm();

        if ($_POST['id'] > 0) {
            $result = $this->my_model->updateJalan();
            $tag = "Edit";
        } else {
            $result = $this->my_model->createJalan();
            $tag = "Tambah";
        }

        if ($result) {
            // TODO: Pindah file dari temporary directory ke direktory Jalan
            foreach ($form as $row) {
                switch ($row['type']) {
                    case 'pdf':
                        if (!empty($_POST[$row['name']])) {
                            FileHandler::MoveFromTemp("pdf/jalan/{$_POST['no_jalan']}", $_POST[$row['name']]);
                        }
                        break;
                    case 'video':
                        if (!empty($_POST[$row['name']])) {
                            FileHandler::MoveFromTemp("video/jalan/{$_POST['no_jalan']}", $_POST[$row['name']]);
                        }
                        break;
                    case 'kml':
                        if (!empty($_POST[$row['name']])) {
                            FileHandler::MoveFromTemp("kml/jalan/{$_POST['no_jalan']}", $_POST[$row['name']], true);
                        }
                        break;
                }
            }

            $result = $this->KoordinatProcess();

            if (!$result) {
                Functions::setDataSession('alert', ["{$tag} Koordinat gagal.", 'danger']);
                Functions::setDataSession('alert', ["{$tag} Jalan success.", 'success']);
            } else {
                $result = $this->DetailProcess();
                if (!$result) {
                    Functions::setDataSession('alert', ["{$tag} Detail Jalan gagal.", 'danger']);
                    Functions::setDataSession('alert', ["{$tag} Koordinat success.", 'success']);
                    Functions::setDataSession('alert', ["{$tag} Jalan success.", 'success']);
                } else {
                    Functions::setDataSession('alert', ["{$tag} Jalan success.", 'success']);
                    $coord = Functions::getDataSession('coordinates', false);

                    // var_dump($coord['final']);
                    foreach ($coord['final'] as $idx => $row) {
                        $row['row'] = $idx + 1;
                        if (!empty($row[6])) {
                            FileHandler::MoveFromTemp("img/jalan/{$_POST['no_jalan']}/{$row['row']}", $row[6], false, true);
                        }
                    }
                }
            }
            $this->model('Data_model')->generateData();
        } else {
            Functions::setDataSession('alert', ["{$tag} Jalan failed.", 'danger']);
        }

        return Functions::getDataSession('alert');
    }

    private function JalanDetail($id)
    {
        return $this->my_model->getJalanDetail($id);
    }

    public function JalanRemove()
    {
        $id = $_POST['id'];


        $result = $this->my_model->deleteJalan($id);
        $tag = 'Remove';
        if ($result) {
            Functions::setDataSession('alert', ["{$tag} Jalan success.", 'success']);
        } else {
            Functions::setDataSession('alert', ["{$tag} Jalan failed.", 'danger']);
        }

        return Functions::getDataSession('alert');
    }

    /**
     * * End Jalan
     */

    /**
     * * Start Koordinat
     */
    public function Koordinat(string $param1 = null, string $param2 = null)
    {
        $this->my_model = $this->model('Jalan_model');
        $this->no_jalan = $param2;

        switch ($param1) {
            case 'search':
                $this->KoordinatSearch();
                break;
            case 'searchori':
                $this->KoordinatOri();
                break;
            case 'setsession':
                $this->KoordinatSetSesion();
                break;
            case 'form':
                $this->KoordinatForm();
                break;
            case 'detail':
                $this->DetailSearch();
                break;
            case 'submit':
                $this->KoordinatSubmit();
                break;
        }
    }

    private function KoordinatSearch()
    {
        $this->KoordinatSetSesion();
        $final = Functions::getDataSession('coordinates', false)[1];

        $perkerasan_opt = $this->options('perkerasan_opt');
        $kondisi_opt = $this->options('kondisi_opt');

        $search = Functions::getSearch();
        $list_koordinat = [];
        foreach ($final as $idx => $row) {
            $row['row'] = $idx + 1;
            $row['perkerasan_text'] = $perkerasan_opt[$row['perkerasan']];
            $row['kondisi_text'] = $kondisi_opt[$row['kondisi']];

            $file = "img/jalan/{$this->no_jalan}/{$row['row']}/{$row['foto']}";
            $row['foto_file'] = '';
            if (!empty($row['foto'])) {
                list($fileurl) = FileHandler::checkFileExist($file);
                $filedir = Functions::getStringBetween($fileurl, UPLOAD_URL, $row['foto']);

                $row['foto_file'] = Functions::getPopupLink($filedir, $row['foto'], null, null, 'fas fa-image');
            }

            if ($idx >= $search['offset'] && $idx <= ($search['offset'] + $search['limit'] - 1)) {
                $list_koordinat[] = $row;
            } elseif (empty($search['offset']) && empty($search['limit'])) {
                $list_koordinat[] = $row;
            }
        }
        Functions::setDataTable($list_koordinat, count($final), count($final));
        exit;
    }

    private function KoordinatOri()
    {
        list($list) = $this->KoordinatJalanSearch($this->no_jalan);
        echo $list['ori'];
        exit;
    }

    private function KoordinatJalanSearch()
    {
        return $this->my_model->getKoordinatJalan($this->no_jalan);
    }

    private function KoordinatBuild(array $coordinates, bool $raw = false)
    {
        $awal       = [];
        $final      = [];
        $ori        = [];
        $segmented  = [];
        foreach ($coordinates as $row) {
            if ($raw) {
                $latitude = number_format($row[1], 8);
                $longitude = number_format($row[0], 8);
                $segment = $row[3];
                $rows = $this->my_model->populateKoordinatDetail($row);
                $rows[0] = $latitude;
                $rows[1] = $longitude;
                $rows[5] = $segment;
                $rows[3] = '';
            } else {
                $rows = $row;
                $row = Functions::buildGeo($row, false);
            }
            $rows = $this->my_model->makeKoordinatDetail($rows);


            if ($rows['segment'] <= 0) {
                $awal[] = $rows;
                $ori[] = $row;
            }
            $final[] = $rows;
            $segmented[] = $row;
        }

        return [$awal, $final, $ori, $segmented];
    }

    private function KoordinatSetSesion()
    {
        $coord = [];
        // ? Corrdinate from Session
        $coord = Functions::getDataSession('coordinates', false);

        // ? Coordinate from Request
        if (!empty($_REQUEST['coordinates'])) {
            $coord = $this->KoordinatBuild($_REQUEST['coordinates'], true);
        } else {
            if (empty($coord)) {
                // ? Coordinates from tdetail_jalan;
                list($detail, $detail_count) = $this->DetailJalanSearch($this->no_jalan);
                if ($detail_count > 0) {
                    $coordinates = [];
                    foreach ($detail as $row) {
                        if (!empty($row['data'])) {
                            foreach (json_decode($row['data']) as $value) {
                                $coordinates[] = $value;
                            }
                        }
                    }
                    $coord = $this->KoordinatBuild($coordinates);
                } else {
                    // ? Coordinates form tkoordinat_jalan;
                    list($coord) = $this->KoordinatJalanSearch($this->no_jalan);
                    $coordinates = (!empty($coord['segmented'])) ? $coord['segmented'] : $coord['ori'];
                    $coordinates = json_decode($coordinates, true);
                    $coord = $this->KoordinatBuild($coordinates, true);
                }
            }
        }

        Functions::setDataSession('coordinates', $coord);
    }

    private function KoordinatProcess()
    {
        list($detail) = $this->my_model->getKoordinatJalan($_POST['no_jalan']);
        if ($detail['no_jalan']) {
            return $this->my_model->updateKoordinat($detail['id']);
        } else {
            return $this->my_model->createKoordinat();
        }
    }

    private function KoordinatForm()
    {
        $data['detail'] = $_POST;
        $data['formId'] = "koordinatForm";
        $data['form'] = $this->my_model->getKoordinatForm();

        $data['main'][] = $this->dofetch('Layout/Form', $data);
        echo json_encode($this->dofetch('Layout/Default', $data));
        exit;
    }

    private function KoordinatSubmit()
    {
        $error = $this->KoordinatValidate();

        if (!$error) {
            echo json_encode($this->KoordinatSet());
        } else {
            echo json_encode($error);
        }
        exit;
    }

    private function KoordinatValidate()
    {
        $form = $this->my_model->getKoordinatForm();
        foreach ($form as $row) {
            $this->validate($_POST, $row, 'Jalan_model', 'koordinat');
        }

        return Functions::getDataSession('alert');
    }

    private function KoordinatSet()
    {
        // var_dump($_POST);
        $values = [];
        foreach ($this->my_model->getKoordinatForm() as $row) {
            if (in_array($row['id'], ['index', 'tag'])) continue;
            $values[$row['id']] = null;
        }
        // var_dump($form);

        foreach ($_POST as $key => $value) {
            $$key = $value;
            if (in_array($key, ['index', 'tag'])) continue;
            $value = (!empty($value)) ? $value : null;
            if (in_array($key, ['lebar', 'segment'])) {
                $value = ($value > 0) ? $value : null;
            }
            $values[$key] = $value;
        }

        // var_dump($values);

        if ($tag == 'edit') {
            $_SESSION['coordinates'][1][$index] = $values;
        }

        Functions::setDataSession('alert', ["Set Koordinat berhasil.", 'success']);
        return Functions::getDataSession('alert');
    }
    /**
     * * End Koordiat
     */

    /**
     * * Start Detail Jalan
     */
    private function DetailJalanSearch($no_jalan)
    {
        return $this->my_model->getDetailJalan($no_jalan);
    }

    private function DetailProcess()
    {
        $this->DetailOld();
        $result = $this->my_model->createDetail();
        if ($result) {
            $this->DetailClear();
        }
        return $result;
    }

    private function DetailOld()
    {
        $this->old_detail = $this->my_model->getOldDetail();
    }

    private function DetailClear()
    {
        if (!empty($this->old_detail)) {
            $this->my_model->clearDetail($this->old_detail);
        }
    }

    private function DetailSearch()
    {
        $final = Functions::getDataSession('coordinates', false)[1];
        $result = [];

        foreach (['perkerasan', 'kondisi'] as $value) {
            $result[$value] = Functions::buildLine($final, $value);
        }

        echo json_encode($result);
        exit;
    }
    /**
     * * End Detail Jalan
     */
}
