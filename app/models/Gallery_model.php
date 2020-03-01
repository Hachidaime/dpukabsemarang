<?php

class Gallery_model extends Database
{
    private $my_tables = ['gallery' => 'tgallery'];
    public $limit = 8;

    public function getTable(string $type = null)
    {
        return Functions::getTable($this->my_tables, $type);
    }

    public function getGalleryForm()
    {
        Functions::setDataSession('form', ['hidden', 'id', 'id', '', []]);
        Functions::setDataSession('form', ['text', 'judul', 'judul', 'Judul', [], true, true]);
        Functions::setDataSession('form', ['date', 'tanggal', 'tanggal', 'Tanggal', [], true, false]);
        Functions::setDataSession('form', ['img', 'upload_gallery', 'upload_gallery', 'Upload Gallery', [], true, false]);

        return Functions::getDataSession('form');
    }

    public function getGalleryThead()
    {
        // TODO: Set column table
        Functions::setDataSession('thead', ['0', 'row', '#']);
        Functions::setDataSession('thead', ['0', 'judul', 'Judul', 'data-halign="center" data-align="left" data-width="200"']);
        Functions::setDataSession('thead', ['0', 'tanggal', 'Tanggal', 'data-halign="center" data-align="left" data-width="200"']);
        Functions::setDataSession('thead', ['0', 'upload_gallery', 'Upload', 'data-halign="center" data-align="left"']);
        Functions::setDataSession('thead', ['0', 'operate']);
        return Functions::getDataSession('thead');
    }

    public function getGallery($public = false)
    {
        if ($public) {
            $page = $_POST['page'];
            $page = ($page) ? $page : 1;
            $offset = ($page - 1) * $this->limit;

            $params['limit'] = $this->limit;
            $params['offset'] = $offset;
        } else {
            $params = [];
            $search = Functions::getSearch();

            $filter = [];

            if (!empty($search['search'])) $filter[] = "judul LIKE '%{$search['search']}%'";
            if (isset($search['limit'])) $params['limit'] = $search['limit'];
            if (isset($search['offset'])) $params['offset'] = $search['offset'];

            $params['filter'] = implode(' AND ', $filter);
        }

        $params['sort'] = "{$this->my_tables['gallery']}.tanggal DESC";

        $query = $this->getSelectQuery($this->my_tables['gallery'], $params);
        $this->execute($query);
        return $this->multiarray();
    }

    public function totalGallery()
    {
        return $this->totalRows($this->my_tables['gallery']);
    }

    public function getGalleryDetail($id)
    {
        $params = [];
        $params['filter'] = "id = ?";
        $query = $this->getSelectQuery($this->my_tables['gallery'], $params);
        $bindVar = [$id];

        $this->execute($query, $bindVar);
        return $this->singlearray();
    }

    public function prepareSaveGallery()
    {
        $values = [];
        $bindVar = [];
        foreach ($_POST as $key => $value) {
            if ($key == 'id') continue;
            $value = ($key == 'tanggal') ? Functions::formatDatetime($value, 'Y-m-d') : $value;
            array_push($values, "{$key}=?");
            array_push($bindVar, $value);
        }
        $values = implode(", ", $values);
        $values .= ", login_id = ?, remote_ip = ?";

        array_push($bindVar, Auth::User('id'), $_SERVER['REMOTE_ADDR']);

        return [$values, $bindVar];
    }
    public function createGallery()
    {
        list($values, $bindVar) = $this->prepareSaveGallery();

        $query = "INSERT INTO {$this->my_tables['gallery']} SET {$values}, update_dt = NOW()";

        $this->execute($query, $bindVar);
        return $this->affected_rows();
    }

    public function updateGallery()
    {
        list($values, $bindVar) = $this->prepareSaveGallery();
        array_push($bindVar, $_POST['id']);

        $query = "UPDATE {$this->my_tables['gallery']} SET {$values}, update_dt = NOW() WHERE id=?";
        $this->execute($query, $bindVar);

        return $this->affected_rows();
    }

    public function deleteGallery($id)
    {
        $query = "DELETE FROM {$this->my_tables['gallery']} WHERE id = ?";
        $bindVar = [$id];
        $this->execute($query, $bindVar);
        return $this->affected_rows();
    }
}
