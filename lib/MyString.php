<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class MyString {

    /*static public function split($length, $string) {
        if (strlen($string) > $length) {
            $string = preg_replace('/\s+?(\S+)?$/', '', substr($string . ' ', 0, $length));
            return $string . "...";
        }
        return $string;
    }*/

	static public function split($len,$str){
        if ($str=="" || $str==NULL) return $str;
        if (is_array($str)) return $str;
        $str = trim($str);
        if (strlen($str) <= $len) return $str;
        $str = substr($str,0,$len);
        if ($str != "") {
            if (!substr_count($str," ")) {
                return $str.' ...';
            }
            while(strlen($str) && ($str[strlen($str)-1] != " ")) {
                $str = substr($str,0,-1);
            }
            $str = substr($str,0,-1);
            $str .= " ...";
        }
        return $str;
    }

    static public function convertVar($string) {
        $marTViet = array("à", "á", "ạ", "ả", "ã", "â", "ầ", "ấ", "ậ", "ẩ", "ẫ", "ă",
            "ằ", "ắ", "ặ", "ẳ", "ẵ", "è", "é", "ẹ", "ẻ", "ẽ", "ê", "ề"
            , "ế", "ệ", "ể", "ễ",
            "ì", "í", "ị", "ỉ", "ĩ",
            "ò", "ó", "ọ", "ỏ", "õ", "ô", "ồ", "ố", "ộ", "ổ", "ỗ", "ơ"
            , "ờ", "ớ", "ợ", "ở", "ỡ",
            "ù", "ú", "ụ", "ủ", "ũ", "ư", "ừ", "ứ", "ự", "ử", "ữ",
            "ỳ", "ý", "ỵ", "ỷ", "ỹ",
            "đ",
            "À", "Á", "Ạ", "Ả", "Ã", "Â", "Ầ", "Ấ", "Ậ", "Ẩ", "Ẫ", "Ă"
            , "Ằ", "Ắ", "Ặ", "Ẳ", "Ẵ",
            "È", "É", "Ẹ", "Ẻ", "Ẽ", "Ê", "Ề", "Ế", "Ệ", "Ể", "Ễ",
            "Ì", "Í", "Ị", "Ỉ", "Ĩ",
            "Ò", "Ó", "Ọ", "Ỏ", "Õ", "Ô", "Ồ", "Ố", "Ộ", "Ổ", "Ỗ", "Ơ"
            , "Ờ", "Ớ", "Ợ", "Ở", "Ỡ",
            "Ù", "Ú", "Ụ", "Ủ", "Ũ", "Ư", "Ừ", "Ứ", "Ự", "Ử", "Ữ",
            "Ỳ", "Ý", "Ỵ", "Ỷ", "Ỹ",
            "Đ",
            " ",
            "!", "@", "#", "$", "%", "^", "&", "*", "(", ")", "-", "+",
            "`", "~", "=", "|", "\\", "/", "{", "}", "[", "]", ";", ":",
            "'", "\"", "<", ">", ",", ".", "?"
        );
        $marKoDau = array("a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a"
            , "a", "a", "a", "a", "a", "a",
            "e", "e", "e", "e", "e", "e", "e", "e", "e", "e", "e",
            "i", "i", "i", "i", "i",
            "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o"
            , "o", "o", "o", "o", "o",
            "u", "u", "u", "u", "u", "u", "u", "u", "u", "u", "u",
            "y", "y", "y", "y", "y",
            "d",
            "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A"
            , "A", "A", "A", "A", "A",
            "E", "E", "E", "E", "E", "E", "E", "E", "E", "E", "E",
            "I", "I", "I", "I", "I",
            "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O"
            , "O", "O", "O", "O", "O",
            "U", "U", "U", "U", "U", "U", "U", "U", "U", "U", "U",
            "Y", "Y", "Y", "Y", "Y",
            "D",
            "_",
            "_", "_", "_", "_", "_", "_", "_", "_", "_", "_", "_", "_",
            "_", "_", "_", "_", "_", "_", "_", "_", "_", "_", "_", "_",
            "_", "_", "_", "_", "_", "_", "_"
        );
        return strtolower(str_replace($marTViet, $marKoDau, trim($string)));
    }

    static public function checkRemoteFileExist($url) {
        $file_headers = @get_headers($url);
        
        // echo "TEST: " . $url . " " . $file_headers[0] . " ";
        
        if ($file_headers[0] == 'HTTP/1.0 404 Not Found' || $file_headers[0] == 'HTTP/1.1 404 Not Found') {
            return false;
        } else {
            return true;
        }
    }

}

?>
