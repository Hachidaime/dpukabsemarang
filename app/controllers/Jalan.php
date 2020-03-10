<?php

/**
 * * app/controllers/Jalan.php
 */
class Jalan extends Controller
{
    private $old_detail;
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

        switch ($param1) {
            case 'search':
                $this->KoordinatSearch($param2);
                break;
            case 'searchori':
                $this->KoordinatAwal($param2);
                break;
            case 'setsession':
                $this->KoordinatSetSesion();
                break;
            case 'form':
                $this->KoordinatForm();
                break;
            case 'submit':
                $this->KoordinatSubmit();
                break;
        }
    }

    private function KoordinatSearch($no_jalan = null)
    {
        $perkerasan_opt = $this->options('perkerasan_opt');
        $kondisi_opt = $this->options('kondisi_opt');

        $search = Functions::getSearch();
        $list_koordinat = [];

        $coord = Functions::getDataSession('coordinates', false);


        // var_dump($koordinat_jalan);
        $used_coord = "";
        if (!empty($coord['final'])) {
            $koordinat = $coord['final'];
            $used_coord = "final1";
        } elseif (!empty($coord['awal'])) {
            $koordinat = $coord['awal'];
            $used_coord = "awal1";
        } else {
            list($koordinat_jalan, $koordinat_count) = $this->KoordinatJalanSearch($no_jalan);
            list($detail_jalan, $detail_count) = $this->DetailJalanSearch($no_jalan);

            if ($detail_count) {
                foreach ($detail_jalan as $idx => $row) {
                    foreach (json_decode($row['koordinat_final'], true) as $value) {
                        $koordinat[] = $value;
                    }
                }
                $used_coord = 'final2';
            } else {
                if ($koordinat_count) {
                    if (!empty($koordinat_jalan['koordinat_final'])) {
                        $koordinat = $koordinat_jalan['koordinat_final'];
                        $used_coord = "final2";
                    } else {
                        if (!empty($koordinat_jalan['koordinat_awal'])) {
                            $koordinat = $koordinat_jalan['koordinat_awal'];
                            $used_coord = "awal2";
                        }
                    }
                    $koordinat = json_decode($koordinat, true); // * row = STRING
                }
            }
        }
        // var_dump($koordinat);
        // exit;

        $coordinates = [];
        foreach ($koordinat as $idx => $row) {
            $row = $this->my_model->populateKoordinatDetail($row);
            $rows = $this->my_model->makeKoordinatDetail($row);
            $rows['perkerasan_text'] = $perkerasan_opt[$rows['perkerasan']];
            $rows['kondisi_text'] = $kondisi_opt[$rows['kondisi']];

            $rows['row'] = $idx + 1;

            // var_dump($rows);
            $file = "img/jalan/{$no_jalan}/{$rows['row']}/{$rows['foto']}";
            // var_dump($file);

            $rows['foto_file'] = '';

            if (!empty($rows['foto'])) {
                list($fileurl) = FileHandler::checkFileExist($file);
                $filedir = Functions::getStringBetween($fileurl, UPLOAD_URL, $rows['foto']);

                $rows['foto_file'] = Functions::getPopupLink($filedir, $rows['foto'], null, null, 'fas fa-image');
            }

            if ($idx >= $search['offset'] && $idx <= ($search['offset'] + $search['limit'] - 1)) {
                array_push($list_koordinat, $rows); // * row = ARRAY
            } elseif (empty($search['offset']) && empty($search['limit'])) {
                array_push($list_koordinat, $rows); // * row = ARRAY
            }

            array_push($coordinates, $row); // * row = STRING
        }

        // var_dump($list_koordinat);
        // var_dump($coordinates);        
        // echo $used_coord;

        if (in_array($used_coord, ['awal1', 'final1'])) {
            $koordinat_awal = $coord['awal'];
        } else {
            if ($used_coord == 'awal2') {
                $koordinat_awal = $coordinates;
            } else {
                $koordinat_awal = [];
                // var_dump($koordinat_jalan['koordinat_awal']);

                foreach (json_decode($koordinat_jalan['koordinat_awal'], true) as $idx => $row) {
                    $row = $this->my_model->populateKoordinatDetail($row);
                    array_push($koordinat_awal, $row); // * row = STRING
                }
            }
        }

        $coord['awal'] = $koordinat_awal;
        $coord['final'] = $coordinates;

        // var_dump($coord['awal']);
        // var_dump($coord['final']);

        Functions::setDataSession('coordinates', $coord);

        Functions::setDataTable($list_koordinat, count($koordinat), count($koordinat));
        exit;
    }

    private function KoordinatAwal(string $no_jalan = null)
    {
        // var_dump($_POST);

        if (!empty($_POST['file'])) {
            if (file_exists(TEMP_UPLOAD_DIR . "/{$_POST['file']}")) {
                echo $this->KoordinatFile();
                exit;
            }
        }

        list($list) = $this->KoordinatJalanSearch($no_jalan);
        // var_dump($list);
        $n = 0;
        foreach (json_decode($list['koordinat_awal'], true) as $idx => $row) {
            $list_koordinat[$n]['row'] = $idx + 1;
            $list_koordinat[$n]['latitude'] = (float) $row[0];
            $list_koordinat[$n]['longitude'] = (float) $row[1];
            $n++;
        }

        echo json_encode($list_koordinat);
        exit;
    }

    private function KoordinatFile()
    {
        $filedir = TEMP_UPLOAD_DIR . "/";
        $filename = $_POST['file'];
        $filepath = $filedir . $filename;

        // $ext = strtolower(pathinfo($filepath, PATHINFO_EXTENSION));

        $data = [];

        $xmlfile = file_get_contents($filepath);
        $xmlfile = str_replace("gx:", "", $xmlfile);
        // $ob= simplexml_load_string($xmlfile);

        $coord_list = trim(Functions::getStringBetween($xmlfile, '<coordinates>', '</coordinates>'));
        if (!empty($coord_list)) {
            $coord = explode(' ', $coord_list);
        }

        foreach ($coord as $key => $value) {
            if (empty(trim($value))) continue;
            // var_dump($value);
            list($longitude, $latitude) = explode(",", $value);
            $data[$key]['latitude'] = trim($latitude);
            $data[$key]['longitude'] = trim($longitude);
            $data[$key]['row'] = $key + 1;
        }
        return json_encode($data);
    }

    private function KoordinatJalanSearch($no_jalan = null)
    {
        return $this->my_model->getKoordinatJalan($no_jalan);
    }

    private function KoordinatSetSesion()
    {
        $coordsegment = $_POST['coordsegment'];

        $coord = Functions::getDataSession('coordinates', false);

        // var_dump($coord['final']);
        // exit;
        if (is_null($coord) || empty($coord) || gettype($coord) === 'NULL') {
            // echo "Test";
            $coord['awal'] = $_POST['coord'];
        } else {
            if (!is_null($coord['final']) || !empty($coord['final']) || gettype($coord['final']) !== 'NULL') {
                unset($coord['awal']);
                foreach ($coord['final'] as $idx => $row) {
                    if ($row[5] > 0) continue;
                    $coord['awal'][] = $row;
                }
            }
        }
        // var_dump($_POST['coord']);
        // var_dump($coord['awal']);

        $koordinat_awal = [];
        foreach ($coord['awal'] as $idx => $row) {
            $row = $this->my_model->populateKoordinatDetail($row);
            $rows = $this->my_model->makeKoordinatDetail($row);
            array_push($koordinat_awal, $rows);
        }

        for ($i = count($coordsegment) - 1; $i >= 0; $i--) {
            $new = array(array($coordsegment[$i][0], $coordsegment[$i][1], 1));
            array_splice($koordinat_awal, $coordsegment[$i][2] + 1, 0, $new);
        }

        $data = [];
        $n = 1;
        foreach ($koordinat_awal as $idx => $row) {
            $rows[$idx] = [];
            $row['latitude'] = (isset($row['latitude'])) ? $row['latitude'] : $row[0];
            $row['longitude'] = (isset($row['longitude'])) ? $row['longitude'] : $row[1];
            $row['segment'] = (isset($row['segment'])) ? $row['segment'] : $row[2];

            array_push($rows[$idx], number_format($row['latitude'], 8)); // ? latitude
            array_push($rows[$idx], number_format($row['longitude'], 8)); // ? longitude
            array_push($rows[$idx], $row['lebar']); // ? lebar
            array_push($rows[$idx], $row['perkerasan']); // ? perkerasan
            array_push($rows[$idx], $row['kondisi']); // ? kondisi
            array_push($rows[$idx], ($row['segment'] > 0) ? $n++ : null); // ? segment
            array_push($rows[$idx], $row['foto']); // ? foto
            array_push($rows[$idx], $row['iri']); // ? iri

            array_push($data, $rows[$idx]);
        }

        Functions::clearDataSession('coordinates');
        $coord['awal'] = $coord['awal'];
        $coord['final'] = $data;
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
        foreach ($_POST as $key => $value) {
            $$key = $value;
            if (in_array($key, ['index', 'tag'])) continue;
            array_push($values, $value);
        }

        if ($tag == 'edit') {
            $_SESSION['coordinates']['final'][$index] = $values;
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
    /**
     * * End Detail Jalan
     */

    /**
     * * Start Generate Jalan
     */
    public function generate(string $param1 = null, string $param2 = null)
    {
        $this->my_model = $this->model('Data_model');

        switch ($param1) {
            case 'search':
                $this->GenerateSearch();
                break;
            case 'add':
                $this->GenerateAdd();
                break;
            case 'edit':
                if (!isset($param2)) Header("Location: " . BASE_URL . "/StaticPage/Error404");
                $this->GenerateEdit($param2);
                break;
            case 'submit':
                $this->GenerateSubmit();
                break;
            case 'remove':
                $this->GenerateRemove($_POST['id']);
                break;
            default:
                $this->GenerateDefault();
                break;
        }
    }

    private function GenerateDefault()
    {
        // TODO: Clear coordinates session
        Functions::clearDataSession('coordinates');

        // TODO: Set title
        Functions::setTitle("Generate Data Jalan");

        // TODO: Load toolbar: add button
        $data['toolbar'][] = $this->dofetch('Component/Button', $this->btn_add);

        // TODO: Load table properties: column name, data-url
        $data['data'] = Functions::defaultTableData();
        $data['thead'] = $this->my_model->getDataThead();
        $data['url'] = BASE_URL . "/Jalan/generate/search";

        // TODO: Load table template
        $table = $this->dofetch('Layout/Table', $data);

        // TODO: Load template
        $data['main'][] = $table;
        $this->view('Layout/Default', $data);
    }

    private function GenerateSearch()
    {
        // TODO: Search Jalan on database: list & total
        list($list, $count) = $this->my_model->getData();
        $total = $this->my_model->totalData();

        // TODO: Prepare data to load on template
        $rows = [];
        foreach ($list as $idx => $row) {
            $row['row'] = Functions::getSearch()['offset'] + $idx + 1;
            array_push($rows, $row);
        }

        // TODO: Echoing data as JSON
        Functions::setDataTable($rows, $count, $total);
        exit;
    }

    private function GenerateAdd()
    {
        Functions::setTitle("Add Generate Data Jalan");

        $detail['name'] = date('YmdHis');
        $detail['name_text'] = $detail['name'];
        $data['detail'] = $detail;

        $data['form'] = $this->my_model->getDataForm();
        $this->form($data);
    }

    private function GenerateEdit($id)
    {
        // TODO: Get Data dari Database
        list($detail, $count) = $this->GenerateDetail($id);
        $detail['name_text'] = $detail['name'];

        // TODO: Set detail Data
        $data['detail'] = $detail;

        // TODO: Cek Data exist
        if ($count <= 0) Header("Location: " . BASE_URL . "/StaticPage/Error404");

        $data['form'] = $this->my_model->getDataForm();
        $this->form($data);
    }

    /**
     * * Data::DataSubmit()
     * ? Submit Form Data
     */
    private function GenerateSubmit()
    {
        // TODO: Get Validasi form Data
        $error = $this->GenerateValidate();

        // TODO: Cek error
        if (!$error) { // ? No Error
            echo json_encode($this->GenerateProcess());
        } else { // ! Error
            echo json_encode($error);
        }
        exit;
    }

    /**
     * * Data::DataValidate()
     * ? Validasi form Data
     */
    private function GenerateValidate()
    {
        // TODO: Get form Data
        $form = $this->my_model->getDataForm();

        foreach ($form as $row) {
            // TODO: Validasi form Data
            $this->validate($_POST, $row, 'Data_model', 'data');
        }

        // TODO: Mengembalikan hasil validasi
        return Functions::getDataSession('alert');
    }

    /**
     * * Data::DataProcess()
     * ? Proses form input
     */
    private function GenerateProcess()
    {
        // TODO: Cek input id
        if ($_POST['id'] > 0) { // ? Id Data exist
            // TODO: Proses edit Data
            $result = $this->my_model->updateData();
            $tag = "Edit";
        } else { // ! Id Data not exist
            $this->GenerateXML();
            exit;
            // TODO: Proses add Data
            $result = $this->my_model->createData();
            $tag = "Add";
        }

        // TODO: Cek hasil proses
        if ($result) { // ? Proses success
            Functions::setDataSession('alert', ["{$tag} Generate Data Jalan success.", 'success']);
        } else { // ! Proses gagal
            Functions::setDataSession('alert', ["{$tag} Generate Data Jalan failed.", 'danger']);
        }

        // TODO: Mengembalikan hasil proses
        return Functions::getDataSession('alert');
    }

    /**
     * * Data::DataDetail($id)
     * ? Get Data detail by id
     * @param id
     * ? Id Data
     */
    private function GenerateDetail($id)
    {
        return $this->my_model->getDataDetail($id);
    }

    /**
     * * Data::DataRemove($id)
     * ? Menghapus Data by id
     * @param id
     * ? Id Data
     */
    public function GenerateRemove($id)
    {
        // TODO: Proses hapus data
        $result = $this->my_model->deleteData($id);
        $tag = 'Remove';

        // TODO: Cek hasil proses hapus
        if ($result) { // ? Hapus Data success
            Functions::setDataSession('alert', ["{$tag} Generate Data Jalan success.", 'success']);
        } else { // ! Hapus Data gagal
            Functions::setDataSession('alert', ["{$tag} Generate Data Jalan failed.", 'danger']);
        }

        // TODO: Mengembalikan hasil proses
        return Functions::getDataSession('alert');
    }

    public function GenerateXML()
    {
        $cond[] = "jenis = 1";
        list($setup_jalan,) = $this->model('Setup_model')->getSetup($cond);
        $list = $this->my_model->getAllDataJalan();

        // ? Style Default
        $m = 1;
        foreach (DEFAULT_ICONSTYLE as $idx => $row) {
            $id = "iconstyle{$m}";
            $style[] = [
                'id' => $id,
                'type' => $row['type'],
                'href' => $row['href']
            ];
            $iconStyle[$idx] = "#{$id}";
        }

        $n = 1;
        foreach (DEFAULT_LINESTYLE as $idx => $row) {
            $id = "linestyle{$n}";
            $style[] = [
                'id' => $id,
                'type' => $row['type'],
                'color' => $row['color'],
                'width' => $row['width']
            ];
            $getStyle[$idx][0][0] = "#{$id}";
            $n += 1;
        }

        $jln_plain['title'] = 'JlnPlain.xml';
        $jln_plain['style'] = $style;
        $jalan = [];
        foreach ($list['jalan'] as $idx => $row) {
            $koordinat = implode(' ', array_map("Functions::makeMapPoint", json_decode($row['koordinat'], true)));
            unset($row['koordinat_final']);
            $row['koordinat'] = $koordinat;
            $row['style'] = $getStyle[$row['kepemilikan']][0][0];
            $jalan[] = $row;
        }

        // ? Style from Setup
        foreach ($setup_jalan as $idx => $row) {
            $id = "linestyle{$n}";
            $style[] = [
                'id' => $id,
                'type' => 'LineStyle',
                'color' => strtoupper(dechex(round($row['opacity'] * 255 / 100)) . str_replace('#', '', $row['warna'])),
                'width' => (!is_null($row['line_width'])) ? $row['line_width'] : 0
            ];
            $getStyle[$row['kepemilikan']][$row['perkerasan']][$row['kondisi']] = "#{$id}";
            $n++;
        }

        $jln_complete['title'] = 'JlnComplete.xml';
        $jln_complete['style'] = $style;

        $jln_perkerasan['title'] = 'JlnPerkerasan.xml';
        $jln_perkerasan['style'] = $style;

        $jln_kondisi['title'] = 'JlnKondisi.xml';
        $jln_kondisi['style'] = $style;

        $segment = [];
        $complete = [];
        $perkerasan = [];
        $kondisi = [];
        $i = 0;
        $j = 0;
        $k = 0;
        $l = 0;
        foreach ($list['detail'] as $idx => $row) {
            $koordinat = implode(' ', array_map("Functions::makeMapPoint", json_decode($row['koordinat'], true)));
            $latitude = $row['latitude'];
            $longitude = $row['longitude'];

            unset($row['koordinat']);
            unset($row['latitude']);
            unset($row['longitude']);

            if ($row['segment'] != $list['detail'][$idx - 1]['segment']) {
                $row['style'] = $iconStyle[1];
                $row['koordinat'] = "{$latitude},{$longitude},0";
                $segment[$i] = $row;
                $i++;
            }
            unset($row['row_id']);
            unset($row['foto']);

            $row['koordinat'] = $koordinat;

            if ($row['perkerasan'] > 0 || $row['kondisi'] > 0) {
                if ($row['no_detail'] > 0 && (($row['no_detail'] - 1) == $list['detail'][$idx - 1]['no_detail'])) {
                    if (($row['perkerasan'] == $list['detail'][$idx - 1]['perkerasan']) && ($row['kondisi'] == $list['detail'][$idx - 1]['kondisi'])) {
                        $complete[$j - 1]['koordinat'] .= $koordinat;
                    } else {
                        $row['style'] = $getStyle[$row['kepemilikan']][$row['perkerasan']][$row['kondisi']];
                        $complete[$j] = $row;
                        $j++;
                    }
                } else {
                    $row['style'] = $getStyle[$row['kepemilikan']][$row['perkerasan']][$row['kondisi']];
                    $complete[$j] = $row;
                    $j++;
                }
            }

            if ($row['perkerasan'] > 0) {
                if ($row['no_detail'] > 0 && (($row['no_detail'] - 1) == $list['detail'][$idx - 1]['no_detail'])) {
                    if ($row['perkerasan'] == $list['detail'][$idx - 1]['perkerasan']) {
                        $perkerasan[$k - 1]['koordinat'] .= $koordinat;
                    } else {
                        $row['style'] = $getStyle[$row['kepemilikan']][$row['perkerasan']][0];
                        $perkerasan[$k] = $row;
                        $k++;
                    }
                } else {
                    $row['style'] = $getStyle[$row['kepemilikan']][$row['perkerasan']][0];
                    $perkerasan[$k] = $row;
                    $k++;
                }
            }

            if ($row['kondisi'] > 0) {
                if ($row['no_detail'] > 0 && (($row['no_detail'] - 1) == $list['detail'][$idx - 1]['no_detail'])) {
                    if ($row['kondisi'] == $list['detail'][$idx - 1]['kondisi']) {
                        $kondisi[$l - 1]['koordinat'] .= $koordinat;
                    } else {
                        $row['style'] = $getStyle[$row['kepemilikan']][0][$row['kondisi']];
                        $kondisi[$l] = $row;
                        $l++;
                    }
                } else {
                    $row['style'] = $getStyle[$row['kepemilikan']][0][$row['kondisi']];
                    $kondisi[$l] = $row;
                    $l++;
                }
            }
        }

        // var_dump($style);
        $jln_plain['line'] = $jalan;
        $jln_plain['segment'] = $segment;
        Functions::saveXML($jln_plain);

        $jln_complete['line'] = array_merge($jalan, $complete);
        $jln_complete['segment'] = $segment;
        Functions::saveXML($jln_complete);

        $jln_perkerasan['line'] = array_merge($jalan, $perkerasan);
        $jln_perkerasan['segment'] = $segment;
        Functions::saveXML($jln_perkerasan);

        $jln_kondisi['line'] = array_merge($jalan, $kondisi);
        $jln_kondisi['segment'] = $segment;
        Functions::saveXML($jln_kondisi);
    }
}
