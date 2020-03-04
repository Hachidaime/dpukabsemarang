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
}
