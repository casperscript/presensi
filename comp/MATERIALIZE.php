<?php

namespace comp;

class MATERIALIZE {

    public static function inputKey($nm_comp, $data) {
        return '<input type="hidden" name="' . $nm_comp . '" id="' . $nm_comp . '" value="' . $data . '">';
    }

    public static function inputText($nm_comp, $type, $data, $opt = '') {
        return '<input type="' . $type . '" id="' . $nm_comp . '" name="' . $nm_comp . '" value="' . $data . '" ' . $opt . '>';
    }

    public static function inputTextArea($nm_comp, $data, $opt = '') {
        return '<textarea id="' . $nm_comp . '" name="' . $nm_comp . '" ' . $opt . '>' . $data . '</textarea>';
    }

    public static function inputSelect($nm_comp, $data, $val, $opt = '') {
        $elmt = '<select id="' . $nm_comp . '" name="' . $nm_comp . '" ' . $opt . '>';
        if (count($data) > 0) {
            foreach ($data as $key => $value) {
                if ($key == $val) {
                    $option[] = '<option value="' . $key . '" selected>' . $value . '</option>';
                } else {
                    $option[] = '<option value="' . $key . '">' . $value . '</option>';
                }
            }
        } else {
            $option = array();
        }
        $set = implode('', $option);
        return $elmt . $set . '</select>';
    }

    public static function inputSelectGrup($nm_comp, $data, $val, $opt = '') {
        $elmt = '<select id="' . $nm_comp . '" name="' . $nm_comp . '" ' . $opt . '>';
        foreach ($data as $g => $grup) {
            if ($grup['grup'] != '') {
                $option[] = ' <optgroup label="' . $grup['grup'] . '">';
                foreach ($grup['data'] as $key => $value) {
                    if ($key == $val)
                        $option[] = '<option value="' . $key . '" selected>' . $value . '</option>';
                    else
                        $option[] = '<option style="" value="' . $key . '">' . $value . '</option>';
                }
            }
            else {
                foreach ($grup['data'] as $key => $value) {
                    if ($key == $val)
                        $option[] = '<option value="' . $key . '" selected>' . $value . '</option>';
                    else
                        $option[] = '<option value="' . $key . '">' . $value . '</option>';
                }
            }

            $option[] = ' </optgroup>';
        }
        $set = implode('', $option);
        return $elmt . $set . '</select>';
    }

    public static function inputRadio($nm_comp, $data, $val) {
        $n = 1; // Hilangkan tag <p> untuk tampilan horizontal
        foreach ($data as $key => $value) {
            if ($key == $val)
                $option[] = '<p class="p-v-xs"><input type="radio" id="' . $nm_comp . $n . '" name="' . $nm_comp . '" value="' . $key . '" class="with-gap" checked /><label for="' . $nm_comp . $n . '">' . $value . '</label></p>';
            else
                $option[] = '<p class="p-v-xs"><input type="radio" id="' . $nm_comp . $n . '" name="' . $nm_comp . '" value="' . $key . '" class="with-gap" /><label for="' . $nm_comp . $n . '">' . $value . '</label></p>';
            $n++;
        }
        $set = implode('', $option);
        return $set;
    }

    public static function inputCheckbox($nm_comp, $data, $val, $opt) {
        $n = 1; // Hilangkan tag <p> untuk tampilan horizontal
        $val = (isset($val)) ? explode(',', $val) : array();
        foreach ($data as $key => $value) {
            if (in_array($value, $val)) {
                $option[] = '<span class="p-v-xs"><input type="checkbox" id="' . $nm_comp . $n . '" name="' . $nm_comp . '" value="' . $value . '" ' . $opt . ' checked /><label for="' . $nm_comp . $n . '">' . ucwords($value) . '</label></span>';
            } else {
                $option[] = '<span class="p-v-xs"><input type="checkbox" id="' . $nm_comp . $n . '" name="' . $nm_comp . '" value="' . $value . '" ' . $opt . '/><label for="' . $nm_comp . $n . '">' . ucwords($value) . '</label></span>';
            }
            $n++;
        }
        $set = implode('', $option);
        return $set;
    }

    public static function pagging($aktif, $batas, $jml_data) {
        $jml_halaman = ceil($jml_data / $batas);
        $link_halaman = '<ul class="pagination">';

        if ($aktif > 1) {
            $prev = $aktif - 1;
            $link_halaman .= '<li class="paging waves-effect" number-page="1"><a href="javascript:void(0)">&laquo;</a></li>
							  <li class="paging waves-effect" number-page="' . $prev . '"><a href="javascript:void(0)">&lsaquo;</a></li>';
        } else {
            $link_halaman .= '<li class="paging waves-effect" number-page="1"><a href="javascript:void(0)">&laquo;</a></li>
							  <li class="paging waves-effect" number-page="1"><a href="javascript:void(0)">&lsaquo;</a></li>';
        }

        $angka = ($aktif > 3 ? '<li>...</li>' : '');
        for ($n = ($aktif - 2); $n < $aktif; $n++) {
            if ($n < 1)
                continue;
            $angka .= '<li class="paging waves-effect" number-page="' . $n . '"><a href="javascript:void(0)">' . $n . '</a></li>';
        }

        $angka .= '<li class="active"><a>' . $n . '</a></li>';
        for ($n = ($aktif + 1); $n < ($aktif + 3); $n++) {
            if ($n > $jml_halaman)
                break;
            $angka .= '<li class="paging waves-effect" number-page="' . $n . '"><a href="javascript:void(0)">' . $n . '</a></li>';
        }

        $angka .= (($aktif + 2) < $jml_halaman ? '<li><span>...</span></li><li class="paging waves-effect" number-page="' . $jml_halaman . '"><a href="javascript:void(0)">' . $jml_halaman . '</a></li>' : ' ');
        $link_halaman .= $angka;

        if ($aktif < $jml_halaman) {
            $next = $aktif + 1;
            $link_halaman .= '<li class="paging waves-effect" number-page="' . $next . '"><a href="javascript:void(0)">&rsaquo;</a></li>
							  <li class="paging waves-effect" number-page="' . $jml_halaman . '"><a href="javascript:void(0)">&raquo;</a></li>';
        } else {
            $link_halaman .= '<li class="paging waves-effect" number-page="' . $jml_halaman . '"><a href="javascript:void(0)">&rsaquo;</a></li>
							  <li class="paging waves-effect" number-page="' . $jml_halaman . '"><a href="javascript:void(0)">&raquo;</a></li>';
        }

        $link_halaman .= '<li class="" style="float:right;">Jumlah Data : ' . $jml_data . '</li>';

        return $link_halaman . '</ul>';
    }

    public static function pagging2($aktif, $batas, $jml_data) {
        $jml_halaman = ceil($jml_data / $batas);
        $awal_halaman = $aktif - 3;
        $ahir_halaman = ($aktif + 3 > $jml_halaman) ? $jml_halaman : $aktif + 3;
        $prev = ($aktif > 1) ? $aktif - 1 : 1;
        $next = ($aktif < $jml_halaman) ? $aktif + 1 : $jml_halaman;

        $link_halaman = '<ul class="pagination">';
        $link_halaman .= '<li class="paging waves-effect" number-page="1"><a href="javascript:void(0)">&laquo;</a></li>';
        $link_halaman .= '<li class="paging waves-effect" number-page="' . $prev . '"><a href="javascript:void(0)">&lsaquo;</a></li>';
        for ($n = $awal_halaman; $n <= $ahir_halaman; $n++) {
            if ($n < 1) {
                continue;
            }
            $selected = ($n == $aktif) ? 'active' : '';
            $link_halaman .= '<li class="paging waves-effect '. $selected . '" number-page="' . $n . '"><a href="javascript:void(0)">' . $n . '</a></li>';
        }
        $link_halaman .= '<li class="paging waves-effect" number-page="' . $next . '"><a href="javascript:void(0)">&rsaquo;</a></li>';
        $link_halaman .= '<li class="paging waves-effect" number-page="' . $jml_halaman . '"><a href="javascript:void(0)">&raquo;</a></li>';

        $link_halaman .= '<li class="" style="float:right;">Jumlah Data : ' . $jml_data . '</li>';

        return $link_halaman . '</ul>';
    }

}

?>
