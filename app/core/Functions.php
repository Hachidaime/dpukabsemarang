<?php

/**
 * * app/core/Functions.php
 */
class Functions
{
    /**
     * * Functions::encrtypt
     * ? Encrypt data
     * @param string $data
     * ? Data to encrypt
     * @param string $key
     * ? Encryption key
     */
    public function encrypt($data, $key = MY_KEY)
    {
        // TODO: Remove the base64 encoding from our key
        $encryption_key = base64_decode($key);
        // TODO: Generate an initialization vector
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
        // TODO: Encrypt the data using AES 256 encryption in CBC mode using our encryption key and initialization vector.
        $encrypted = openssl_encrypt($data, 'aes-256-cbc', $encryption_key, 0, $iv);
        // TODO: The $iv is just as important as the key for decrypting, so save it with our encrypted data using a unique separator (::)
        return base64_encode($encrypted . '::' . $iv);
    }

    /**
     * * Functions::decrtypt
     * ? Decrypt data
     * @param string data
     * ? Data to decrypt
     * @param string $key
     * ? Encryption key
     */
    public function decrypt($data, $key = MY_KEY)
    {
        // TODO: Remove the base64 encoding from our key
        $encryption_key = base64_decode($key);
        // TODO: To decrypt, split the encrypted data from our IV - our unique separator used was "::"
        list($encrypted_data, $iv) = explode('::', base64_decode($data), 2);
        return openssl_decrypt($encrypted_data, 'aes-256-cbc', $encryption_key, 0, $iv);
    }

    /**
     * * Functions::setTitle
     * ? Set Title & Title Bar
     * @param string $string
     * ? Title
     */
    public function setTitle(string $string = "Untitled")
    {
        // Todo: Cek session Admin dan User
        if (isset($_SESSION['admin']) && isset($_SESSION['USER'])) {
            // TODO: Set Title Bar Admin
            $_SESSION['title_bar'] = "{$string} - Admin Console";
        } else {
            // TODO: Set Title Bar Public
            $_SESSION['title_bar'] = "{$string} - " . PROJECT_NAME;
        }

        // TODO: Set Judul halaman
        $_SESSION['title'] = $string;
    }

    /**
     * * Functions::setList
     * ? Set list
     * @param array $array
     * ? array
     */
    public function setList($array)
    {
        $list = [];
        foreach ($array as $idx => $row) {
            $row['row'] = $idx + 1;
            $list[$idx] = $row;
        }
        return $list;
    }

    /**
     * * Functions::getSearch
     * ? Get parameter from Bootstrap Table search
     */
    public function getSearch()
    {
        // TODO: Get parameter
        $params = ltrim(strstr($_SERVER['REQUEST_URI'], '?'), '?');

        // TODO: Format parameter
        $rows = [];
        foreach (explode("&", $params) as $idx => $row) {
            list($key, $val) = explode("=", $row);
            $rows[$key] = urldecode($val);
        }

        // TODO: Return result
        return $rows;
    }

    /**
     * * Functions::setDataSession
     * ? Set $_SESSION values
     * @param string $type
     * ? type
     * @param string $params
     * ? parameter
     */
    public function setDataSession(string $type, $params = [])
    {
        switch ($type) {
            case 'alert':
                /**
                 * TODO: Set $_SESSION['alert']
                 * ? Use for alert
                 * @param string message
                 * ? Alert Message
                 * @param string alert
                 * ? Alert Type: Primary, Success, Danger, Warning, Secondary, Info
                 */
                list($message, $alert) = $params;
                $_SESSION[$type][$alert][] = $message;
                break;
            case 'form':
                /**
                 * TODO: Set $_SESSION['form']
                 * ? Use for form input
                 * @param string formtype
                 * ? Input type
                 * @param string id
                 * ? Input id
                 * @param string name
                 * ? Input name
                 * @param string label
                 * ? Input label
                 * @param boolean required
                 * ? Input is required
                 * @param boolean unique
                 * ? Input is unique
                 * @param string helper
                 * ? Input helper
                 */
                list($formtype, $id, $name, $label, $options, $required, $unique, $helper) = $params;
                $_SESSION[$type][] = [
                    'type'          => $formtype,
                    'id'            => $id,
                    'name'          => $name,
                    'label'         => $label,
                    'options'       => $options,
                    'required'      => $required,
                    'unique'        => $unique,
                    'helper'        => $helper
                ];
                break;
            case 'thead':
                /**
                 * TODO: Set $_SESSION['thead']
                 * ? Use for Bootstrap Table column name
                 * @param int row
                 * ? Row position, start from 0.
                 * @param string field
                 * ? Field name
                 * @param string title
                 * ? Table column title
                 * @param string data
                 * ? Column properties
                 */
                list($row, $field, $title, $data) = $params;
                $_SESSION[$type][$row][] = [
                    'field' => $field,
                    'title' => $title,
                    'data'  => $data
                ];
                break;
            default:
                /**
                 * TODO: Set $_SESSION[$type]
                 * ? Set session
                 */
                $_SESSION[$type] = $params;
                break;
        }
    }

    /**
     * * Functions::getDataSession
     * ? Get $_SESSION values
     * @param string $type
     * ? type
     * @param boolean $clear
     * ? Check clear session. Default: TRUE.
     */
    public function getDataSession(string $type, bool $clear = true)
    {
        $data = $_SESSION[$type];
        // TODO: Cek clear session.
        if ($clear) self::clearDataSession($type);
        return $data;
    }

    /**
     * * Functions::clearDataSession
     * ? Get $_SESSION values
     * @param string type
     * ? type
     */
    public function clearDataSession(string $type)
    {
        unset($_SESSION[$type]);
    }

    /**
     * * Functions::parseURL
     * ? Parsing URL dari $_GET['url];
     */
    public function parseURL()
    {
        // TODO: Cek URL Admin
        if (strpos($_GET['url'], 'Admin') !== false) {
            // TODO: Set Session Admin
            self::setDataSession('admin', true);
        } else {
            // TODO: Unset Session Admin
            self::clearDataSession('admin');
        }

        // TODO: Cek $_GET['url'] exist
        if (isset($_GET['url'])) {
            $url = ltrim($_GET['url'], 'Admin');
            $url = ltrim($url, '/');
            $url = rtrim($url, '/');
            $url = filter_var($url, FILTER_SANITIZE_URL);
            $url = explode("/", $url);
            return $url;
        }
    }

    /**
     * * Functions::formatDatetime
     * ? Formatting Datetime
     * @param string $dt
     * ? Input datatime. Format date: 'd/m/Y', 'Y-m-d'.
     * @param string $format
     * ? Format Output datetime
     */
    public function formatDatetime(string $dt, string $format)
    {
        if (strpos($dt, "/") !== false) {
            $dt = implode("-", array_reverse(explode("/", $dt)));
        }
        return date($format, strtotime($dt));
    }

    /**
     * * Functions::genOptions
     * ? Change 3D Array to 2D Array
     * @param array $array
     * ? 3D Array
     * @param string $key
     * ? Variable as array key
     * @param string $value
     * ? Variable as array value
     */
    public function genOptions(array $array, string $value, string $key = null)
    {
        $options = [];
        foreach ($array as $row) {
            if ($key) {
                $options[$row[$key]] = $row[$value];
            } else {
                array_push($options, $row[$value]);
            }
        }

        return $options;
    }

    /**
     * * Functions::getPopupLink
     * ? Get Popup Link
     * @param string $directory
     * @param string $filename
     * @param string $title
     * @param string $footer
     */
    public function getPopupLink($directory, $filename, $title, $footer, $icon = null)
    {
        $data_title = (!empty($title)) ? "data-title=\"{$title}\"" : "";
        $data_footer = (!empty($footer)) ? "data-footer=\"{$footer}\"" : "";

        $file_url = UPLOAD_URL . "{$directory}/{$filename}";

        if (!is_null($icon)) {
            $text = "<i class=\"{$icon}\"></i>";
        } else {
            $text = $filename;
        }

        return "<a href=\"{$file_url}\" data-toggle=\"lightbox\" {$data_title} {$data_footer}>{$text}</a>";
    }

    /**
     * * Functions::makeButton
     * ? Making button
     * @param string $tag
     * ? HTML tag as button
     * @param string $id
     * ? Button id
     * @param string $html
     * ? Button content
     * @param string $color
     * ? Button color, use bootstrap Color
     * @param string $class
     * ? Button class
     * @param int $width
     * ? Button width
     */
    public function makeButton(string $tag, string $id, string $html, string $color = null, string $class = null, int $width = 150)
    {
        $array = [
            'tag' => $tag,
            'id' => $id,
            'html' => $html,
            'color' => $color,
            'class' => $class,
            'width' => $width,
        ];

        return $array;
    }

    /**
     * * Functions::getTable
     * ? Get table name from table list
     * @param array $tables
     * ? Table list
     * @param string $type
     * ? Type
     */
    public function getTable(array $tables, string $type = null)
    {
        if (!is_null($type)) {
            $table = $tables[$type];
        } else {
            $table = $tables;
        }
        return $table;
    }

    /**
     * * Functions::setDataTable
     * ? Set data to Bootstrap Table
     * @param array $rows
     * ? Table rows
     * @param int $count
     * @param int $total
     */
    public function setDataTable(array $rows, int $count,  int $total)
    {
        $result = [];
        $search = self::getSearch();

        if ($count >= $search['limit']) {
            $result['total'] = $count;
        }
        $result['totalNotFiltered'] = $total;
        $result['rows'] = $rows;
        echo json_encode($result);
    }

    /**
     * * Functions::getStringBetween
     * ? Get string between 2 string
     * @param string $str
     * @param string $from
     * @param string $to
     */
    public function getStringBetween(string $str, string $from, string $to)
    {

        $string = substr($str, strpos($str, $from) + strlen($from));

        if (strstr($string, $to, TRUE) != FALSE) {

            $string = strstr($string, $to, TRUE);
        }

        return $string;
    }

    /**
     * * Functions::makeTableData
     * ? Setting data properties on DataTable
     * @param array $data
     */
    public function makeTableData(array $data)
    {
        foreach ($data as $key => $value) {
            $result[] = "data-{$key}=\"{$value}\"";
        }

        return implode(' ', $result);
    }

    /**
     * * Functions::defaultTableData
     * ? Get default data properties on DataTable
     */
    public function defaultTableData()
    {
        $data = DEFAULT_TABLE_DATA;
        // var_dump($data);
        return self::makeTableData($data);
    }

    public function createKML($data, &$dom)
    {
        // Creates the root KML element and appends it to the root document.
        $node = $dom->createElementNS('http://earth.google.com/kml/2.1', 'kml');
        $parNode = $dom->appendChild($node);

        // Creates a KML Document element and append it to the KML element.
        $dnode = $dom->createElement('Document');
        $docNode = $parNode->appendChild($dnode);

        // Create style elements
        if ($data['style']) {
            foreach ($data['style'] as $idx => $row) {
                $restStyleNode = $dom->createElement('Style');
                $restStyleNode->setAttribute('id', $row['id']);

                switch ($row['type']) {
                    case 'LineStyle':
                        $restLinestyleNode = $dom->createElement('LineStyle');
                        $restColor = $dom->createElement('color', $row['color']);
                        $restWidth = $dom->createElement('width', $row['width']);
                        $restLinestyleNode->appendChild($restColor);
                        $restLinestyleNode->appendChild($restWidth);
                        $restStyleNode->appendChild($restLinestyleNode);
                        break;
                    case 'IconStyle':
                        $restIconstyleNode = $dom->createElement('IconStyle');
                        $restIconNode = $dom->createElement('Icon');
                        $restHref = $dom->createElement('href', $row['href']);
                        $restIconNode->appendChild($restHref);
                        $restIconstyleNode->appendChild($restIconNode);
                        $restStyleNode->appendChild($restIconstyleNode);
                        break;
                }
                $docNode->appendChild($restStyleNode);
            }
        }

        if ($data['line']) {
            foreach ($data['line'] as $idx => $row) {
                // Creates a Placemark and append it to the Document.
                $node = $dom->createElement('Placemark');
                $placeNode = $docNode->appendChild($node);
                // Creates an id attribute and assign it the value of id column.
                // $placeNode->setAttribute('id', 'placemark' . $row['id']);

                // Create name, and description elements and assigns them the values of the name and address columns from the results.
                $nameNode = $dom->createElement('name', htmlentities($row['nama_jalan']));
                $placeNode->appendChild($nameNode);
                $descNode = $dom->createElement('description', "testing");
                $placeNode->appendChild($descNode);
                $styleUrl = $dom->createElement('styleUrl', "{$row['style']}");
                $placeNode->appendChild($styleUrl);

                // Creates a LineString element.
                $lineStringNode = $dom->createElement('LineString');
                $placeNode->appendChild($lineStringNode);

                // Creates a coordinates element and gives it the value of the lng and lat columns from the results.
                $coorNode = $dom->createElement('coordinates', $row['koordinat']);
                $lineStringNode->appendChild($coorNode);
            }
        }

        if ($data['segment']) {
            foreach ($data['segment'] as $idx => $row) {
                // Creates a Placemark and append it to the Document.
                $node = $dom->createElement('Placemark');
                $placeNode = $docNode->appendChild($node);
                // Creates an id attribute and assign it the value of id column.
                // $placeNode->setAttribute('id', 'placemark' . $row['id']);

                // Create name, and description elements and assigns them the values of the name and address columns from the results.
                $nameNode = $dom->createElement('name', htmlentities($row['nama_jalan']));
                $placeNode->appendChild($nameNode);
                $descNode = $dom->createElement('description', "testing");
                $placeNode->appendChild($descNode);
                $styleUrl = $dom->createElement('styleUrl', "{$row['style']}");
                $placeNode->appendChild($styleUrl);

                // Creates a Point element.
                $pointNode = $dom->createElement('Point');
                $placeNode->appendChild($pointNode);

                // Creates a coordinates element and gives it the value of the lng and lat columns from the results.
                $coorNode = $dom->createElement('coordinates', $row['koordinat']);
                $pointNode->appendChild($coorNode);
            }
        }
    }

    public function saveXML($data)
    {
        // Creates the Document.
        $dom = new DOMDocument('1.0', 'UTF-8');
        self::createKML($data, $dom);

        $filedir = DOC_ROOT . "data/{$_POST['name']}";
        FileHandler::createWritableFolder($filedir);
        // $dom->saveXML();
        $dom->save("{$filedir}/{$data['title']}");
    }

    public function makeMapPoint(array $point)
    {
        $point = array_reverse($point);
        array_push($point, 0);
        return implode(",", $point);
    }

    public function saveJSON($data)
    {
        $filedir = DOC_ROOT . "data/{$_POST['name']}";
        FileHandler::createWritableFolder($filedir);

        $myfile = fopen("{$filedir}/{$data['title']}", "w") or die("Unable to open file!");
        fwrite($myfile, json_encode($data['content']));
        fclose($myfile);
    }

    public function getParams(array $data)
    {
        foreach ($data as $key => $value) {
            if (in_array($key, ['select', 'sort'])) {
                $params[$key] = implode(", ", $value);
            } else {
                $params[$key] = implode(" ", $value);
            }
        }
        return $params;
    }

    public function getLineFromJalan(array $data, array $style, string $kepemilikan = null)
    {
        foreach ($data as $row) {
            if (!is_null($kepemilikan)) {
                if ($row['kepemilikan'] != $kepemilikan) continue;
            }
            $koordinat = implode(' ', array_map("Functions::makeMapPoint", json_decode($row['koordinat'], true)));
            unset($row['koordinat_final']);
            $row['koordinat'] = $koordinat;
            $row['style'] = $style[$row['kepemilikan']][0][0];
            $line[] = $row;
        }

        return $line;
    }

    public function getLineFromDetail(array $data, array $lineStyle, array $iconStyle, string $kepemilikan = null)
    {
        $segment = [];
        $complete = [];
        $perkerasan = [];
        $kondisi = [];
        $i = 0;
        $j = 0;
        $k = 0;
        $l = 0;

        foreach ($data as $idx => $row) {
            if (!is_null($kepemilikan)) {
                if ($row['kepemilikan'] != $kepemilikan) continue;
            }

            $koordinat = implode(' ', array_map("Functions::makeMapPoint", json_decode($row['koordinat'], true)));
            $latitude = $row['latitude'];
            $longitude = $row['longitude'];

            unset($row['koordinat']);
            unset($row['latitude']);
            unset($row['longitude']);

            if ($row['segment'] != $data[$idx - 1]['segment']) {
                $row['style'] = $iconStyle[1];
                $row['koordinat'] = "{$longitude},{$latitude},0";
                $segment[$i] = $row;
                $i++;
            }
            unset($row['row_id']);
            unset($row['foto']);

            $row['koordinat'] = $koordinat;

            if ($row['perkerasan'] > 0 || $row['kondisi'] > 0) {
                if ($row['no_detail'] > 0 && (($row['no_detail'] - 1) == $data[$idx - 1]['no_detail'])) {
                    if (($row['perkerasan'] == $data[$idx - 1]['perkerasan']) && ($row['kondisi'] == $data[$idx - 1]['kondisi'])) {
                        $complete[$j - 1]['koordinat'] .= $koordinat;
                    } else {
                        $row['style'] = $lineStyle[$row['kepemilikan']][$row['perkerasan']][$row['kondisi']];
                        $complete[$j] = $row;
                        $j++;
                    }
                } else {
                    $row['style'] = $lineStyle[$row['kepemilikan']][$row['perkerasan']][$row['kondisi']];
                    $complete[$j] = $row;
                    $j++;
                }
            }

            if ($row['perkerasan'] > 0) {
                if ($row['no_detail'] > 0 && (($row['no_detail'] - 1) == $data[$idx - 1]['no_detail'])) {
                    if ($row['perkerasan'] == $data[$idx - 1]['perkerasan']) {
                        $perkerasan[$k - 1]['koordinat'] .= $koordinat;
                    } else {
                        $row['style'] = $lineStyle[$row['kepemilikan']][$row['perkerasan']][0];
                        $perkerasan[$k] = $row;
                        $k++;
                    }
                } else {
                    $row['style'] = $lineStyle[$row['kepemilikan']][$row['perkerasan']][0];
                    $perkerasan[$k] = $row;
                    $k++;
                }
            }

            if ($row['kondisi'] > 0) {
                if ($row['no_detail'] > 0 && (($row['no_detail'] - 1) == $data[$idx - 1]['no_detail'])) {
                    if ($row['kondisi'] == $data[$idx - 1]['kondisi']) {
                        $kondisi[$l - 1]['koordinat'] .= $koordinat;
                    } else {
                        $row['style'] = $lineStyle[$row['kepemilikan']][0][$row['kondisi']];
                        $kondisi[$l] = $row;
                        $l++;
                    }
                } else {
                    $row['style'] = $lineStyle[$row['kepemilikan']][0][$row['kondisi']];
                    $kondisi[$l] = $row;
                    $l++;
                }
            }
        }

        return [$segment, $complete, $perkerasan, $kondisi];
    }
}
