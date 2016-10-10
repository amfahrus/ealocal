<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class mod_voucherin extends CI_Controller {

    private $_userconfig = array();

    public function __construct() {
        parent::__construct();
        session_start();
        $this->load->library('myauth');
        if (!$this->myauth->logged_in()) {
            if (IS_AJAX) {
                header('HTTP/1.1 401 Unauthorized');
                exit;
            } else {
                $this->session->set_userdata('redir', current_url());
                redirect('mod_user/user_auth');
            }
        }
        $this->myauth->has_role();
        $this->load->model('voucherin_model');
        $this->load->model('dataset_db');
        $this->load->library("searchform");
        $this->_userconfig = $this->dataset_db->getUserconfig($this->session->userdata('ba_user_id'));
    }

    public function index() {
        $this->list_jurnal();
        /*
          $this->toolbar->create_toolbar();
          $this->toolbar->cGroupButton();
          $this->toolbar->addLink("", "btn tooltips", "#", "form_voucherin_delete", "cus-application", "List Import Data", "tooltip", "right");
          $this->toolbar->addLink("", "btn tooltips", "#", "form_voucherin_delete", "cus-application", "List Import Data", "tooltip", "right");
          $this->toolbar->eGroupButton();
          $data['toolbars'] = $this->toolbar->generate();

          $data['ptitle'] = "Voucher In";
          $data['navs'] = $this->dataset_db->buildNav(0);
          $tabs = $this->session->userdata('tabs');
          if (!$tabs)
          $tabs = array();
          $tabs['mod_voucherin'] = $this->dataset_db->getModule('mod_voucherin');
          $this->session->set_userdata('tabs', $tabs);
          $data['current_tab'] = $tabs['mod_voucherin']['link'];
          $data['content'] = $this->load->view('voucherin', $data, true);
          $this->load->vars($data);
          $this->load->view('default_view');
         * */
    }

    public function edit_jurnal() {

        $this->form_validation->set_rules("nobukti", "Nomor Bukti", "required|xss_clean");
        if ($this->form_validation->run() == TRUE) {
            if (isset($_SESSION["transaksi"]) AND !empty($_SESSION["transaksi"])) {
                unset($_SESSION["transaksi"]);
            }

            if (!isset($_SESSION["transaksi"])) {
                $_SESSION["transaksi"] = array();
            }

            $nobukti = $this->input->post("nobukti");
            $this->voucherin_model->get_nobukti($nobukti);
            $jurnal = $this->voucherin_model->getArrayNobukti();
			//die(print_r($jurnal));
            $_SESSION["transaksi"] = $jurnal;

            $data['redirect'] = base_url() . 'mod_voucherin/test';

            $json['json'] = $data;
            $this->load->view('template/ajax', $json);
        } else {
            $data['error'] = validation_errors();
            $json['json'] = $data;
            $this->load->view('template/ajax', $json);
        }

    }

    public function sess2json() {
        if (!empty($_SESSION["transaksi"]) AND isset($_SESSION["transaksi"]) AND $_SESSION["transaksi"]["jurnal"]["jenis_jurnal"] == 1) {
            $temp_result = array();
            $no = 1;
            $debet = 0;
            $kredit = 0;

            foreach ($_SESSION["transaksi"] as $keys => $value) {
                $temp_result[] = array(
                    "act" => "<a href=\"#\" onclick=\"voucherin_getSession();\" class=\"link_edit\"><img  src=\"" . base_url() . "media/edit.png\" /></a>",
                    "id" => 0,
                    "check" => "<input type=\"checkbox\" value=\"" . $value["no_dokumen"] . "\" name=\"jq_checkbox_added[]\" class=\"jq_checkbox_added\" />",
                    "no" => $value["no_dokumen"],
                    "tanggal" => $value["tanggal"],
                    "keterangan" => "",
                    "dperkir_id" => "",
                    "kdperkiraan" => "",
                    "bukubantu" => "",
                    "debet" => "",
                    "kredit" => ""
                );
                foreach ($value["detail"] as $key2 => $value2) {
                    foreach ($value2["detail"] as $key => $value3) {
                        if ($key === "D") {
                            $temp_result[] = array(
                                "act" => "",
                                "id" => $no,
                                "check" => "",
                                "no" => "",
                                "tanggal" => "",
                                "keterangan" => $value2["keterangan"],
                                "dperkir_id" => $value3["dperkir_id"],
                                "kdperkiraan" => $value3["kdperkiraan"],
                                "bukubantu" => $value3["bukubantu"],
                                "debet" => $value3["debet"],
                                "kredit" => $value3["kredit"]
                            );
                            $no++;
                        } else {
                            $temp_result[] = array(
                                "act" => "",
                                "id" => $no,
                                "check" => "",
                                "no" => "",
                                "tanggal" => "",
                                "keterangan" => "",
                                "dperkir_id" => $value3["dperkir_id"],
                                "kdperkiraan" => $value3["kdperkiraan"],
                                "bukubantu" => $value3["bukubantu"],
                                "debet" => $value3["debet"],
                                "kredit" => $value3["kredit"]
                            );
                            $no++;
                        }
                        $debet = $debet + $value3["debet"];
                        $kredit = $kredit + $value3["kredit"];
                    }
                }
            }

            //echo "<pre>";
            //print_r($temp_result);

            $responce['page'] = 1;
            $responce['total'] = 1;
            $responce['records'] = 1;
            $responce['userdata']['debet'] = $debet;
            $responce['userdata']['kredit'] = $kredit;

            $i = 0;
            foreach ($temp_result as $row) {
                $responce['rows'][$i]['id'] = $row["id"];
                $responce['rows'][$i]['cell'] = array(
                    $row["act"],
                    $row["id"],
                    $row["check"],
                    $row["no"],
                    $row["tanggal"],
                    $row["keterangan"],
                    $row["dperkir_id"],
                    $row["kdperkiraan"],
                    $row["bukubantu"],
                    $row["debet"],
                    $row["kredit"]
                );
                $i++;
            }
            echo json_encode($responce);
        } else {
            $responce['page'] = 1;
            $responce['total'] = 1;
            $responce['records'] = 1;
            $responce['userdata']['debet'] = 0;
            $responce['userdata']['kredit'] = 0;
            echo json_encode($responce);
        }
    }

    public function add2session() {
		$id = $this->input->post("id");
		$nobukti = $this->input->post("nobukti");
		$tanggal = $this->input->post("tanggal");
		$jenis = $this->input->post("jenis");
		$no_dokumen = $this->input->post("no_dokumen");
		$debet_id = $this->input->post("debet_id");
		$debet_kode = $this->input->post("debet_kode");
		$debet_bukubantu = $this->input->post("debet_bukubantu");
		$kredit_id = $this->input->post("kredit_id");
		$kredit_kode = $this->input->post("kredit_kode");
		$kredit_bukubantu = $this->input->post("kredit_bukubantu");
		$keterangan = $this->input->post("keterangan");
		$nilai = $this->input->post("nilai");
		$is_rekanan_debet = $this->input->post("is_rekanan_debet");
		$is_sbdaya_debet = $this->input->post("is_sbdaya_debet");
		$is_proyek_debet = $this->input->post("is_proyek_debet");
		$is_rekanan_kredit = $this->input->post("is_rekanan_kredit");
		$is_sbdaya_kredit = $this->input->post("is_sbdaya_kredit");
		$is_proyek_kredit = $this->input->post("is_proyek_kredit");

        $this->form_validation->set_rules("id", "id", "numeric|xss_clean");
        $this->form_validation->set_rules("tanggal", "tanggal", "required");
        $this->form_validation->set_rules("jenis", "jenis", "required");
        $this->form_validation->set_rules("debet_id", "Id Debet", "required");
        $this->form_validation->set_rules("debet_kode", "Kode Debet", "required");
        $this->form_validation->set_rules("debet_bukubantu", "Buku Bantu Debet", "");
        $this->form_validation->set_rules("no_dokumen", "Nomor Dokumen", "required");
        $j = 0;
        $u = 1;
        foreach ($kredit_id as $arr) {
            $this->form_validation->set_rules("kredit_id[$j][value]", "Id Kredit ke $u", "required");
            $this->form_validation->set_rules("kredit_kode[$j][value]", "Kode Kredit ke $u", "required");
			if((!empty($is_rekanan_kredit[$j]["value"]) || !empty($is_sbdaya_kredit[$j]["value"]) || !empty($is_proyek_kredit[$j]["value"])) && empty($kredit_bukubantu[$j]["value"])){
				$this->form_validation->set_rules("kredit_bukubantu[$j][value]", "Buku Bantu Kredit ke $u", "required");
			}
            $this->form_validation->set_rules("keterangan[$j][value]", "Keterangan ke $u", "required");
            $this->form_validation->set_rules("nilai[$j][value]", "Nilai ke $u", "required");
            $j++;
            $u++;
        }
        if ($this->form_validation->run() == TRUE) {

            //die(print_r($nilai));
            unset($_SESSION["transaksi"]);
            if (!isset($_SESSION["transaksi"])) {
                $_SESSION["transaksi"] = array();
            }

            //if (!empty($_SESSION["transaksi"]["jurnal"]["detail"]) AND is_array($_SESSION["transaksi"]["jurnal"]["detail"])) {
            //$x = count($_SESSION["transaksi"]["jurnal"]["detail"]) + 1;
            if (!empty($id)) {
                $x = $id;
            } else {
                $x = 1;
            }

            if (strtolower($is_rekanan_debet) == "t") {
                $kdnasabah_debet = $debet_bukubantu;
                $kdsbdaya_debet = "";
            } elseif (strtolower($is_sbdaya_debet) == "t") {
                $kdnasabah_debet = "";
                $kdsbdaya_debet = $debet_bukubantu;
            } elseif (strtolower($is_proyek_debet) == "t") {
                $kdnasabah_debet = $debet_bukubantu;
                $kdsbdaya_debet = "";
            } else {
                $kdnasabah_debet = "";
                $kdsbdaya_debet = "";
            }

            $_SESSION["transaksi"]["jurnal"]["tanggal"] = $tanggal;
            $_SESSION["transaksi"]["jurnal"]["no_dokumen"] = $no_dokumen;
            $_SESSION["transaksi"]["jurnal"]["jenis_transaksi"] = $jenis;
            $_SESSION["transaksi"]["jurnal"]["jenis_jurnal"] = 1;
            $_SESSION["transaksi"]["jurnal"]["nobukti"] = $nobukti;
            $_SESSION["transaksi"]["jurnal"]["id_proyek"] = "";
            $_SESSION["transaksi"]["jurnal"]["is_approved"] = "";
            $i = 0;
            foreach ($kredit_id as $val) {

                if (strtolower($is_rekanan_kredit[$i]["value"]) == "t") {
                    $kdnasabah_kredit[$i]["value"] = $kredit_bukubantu[$i]["value"];
                    $kdsbdaya_kredit[$i]["value"] = "";
                } elseif (strtolower($is_sbdaya_kredit[$i]["value"]) == "t") {
                    $kdnasabah_kredit[$i]["value"] = "";
                    $kdsbdaya_kredit[$i]["value"] = $kredit_bukubantu[$i]["value"];
                } elseif (strtolower($is_proyek_kredit[$i]["value"]) == "t") {
                    $kdnasabah_kredit[$i]["value"] = $kredit_bukubantu[$i]["value"];
                    $kdsbdaya_kredit[$i]["value"] = "";
                } else {
                    $kdnasabah_kredit[$i]["value"] = "";
                    $kdsbdaya_kredit[$i]["value"] = "";
                }

                $_SESSION["transaksi"]["jurnal"]["detail"][$x]["gid"] = "";
                $_SESSION["transaksi"]["jurnal"]["detail"][$x]["keterangan"] = $keterangan[$i]["value"];
                $_SESSION["transaksi"]["jurnal"]["detail"][$x]["nilai"] = $nilai[$i]["value"];

                $_SESSION["transaksi"]["jurnal"]["detail"][$x]["detail"]["D"]["id_tempjurnal"] = "";
                $_SESSION["transaksi"]["jurnal"]["detail"][$x]["detail"]["D"]["dperkir_id"] = $debet_id;
                $_SESSION["transaksi"]["jurnal"]["detail"][$x]["detail"]["D"]["kdperkiraan"] = $debet_kode;
                $_SESSION["transaksi"]["jurnal"]["detail"][$x]["detail"]["D"]["kdnasabah"] = $kdnasabah_debet;
                $_SESSION["transaksi"]["jurnal"]["detail"][$x]["detail"]["D"]["kdsbdaya"] = $kdsbdaya_debet;
                $_SESSION["transaksi"]["jurnal"]["detail"][$x]["detail"]["D"]["is_rekanan"] = $is_rekanan_debet;
                $_SESSION["transaksi"]["jurnal"]["detail"][$x]["detail"]["D"]["is_sbdaya"] = $is_sbdaya_debet;
                $_SESSION["transaksi"]["jurnal"]["detail"][$x]["detail"]["D"]["is_proyek"] = $is_proyek_debet;
                $_SESSION["transaksi"]["jurnal"]["detail"][$x]["detail"]["D"]["bukubantu"] = $debet_bukubantu;
                $_SESSION["transaksi"]["jurnal"]["detail"][$x]["detail"]["D"]["dk"] = "D";
                $_SESSION["transaksi"]["jurnal"]["detail"][$x]["detail"]["D"]["volume"] = "";
                $_SESSION["transaksi"]["jurnal"]["detail"][$x]["detail"]["D"]["debet"] = $nilai[$i]["value"];
                $_SESSION["transaksi"]["jurnal"]["detail"][$x]["detail"]["D"]["kredit"] = 0;

                $_SESSION["transaksi"]["jurnal"]["detail"][$x]["detail"]["K"]["id_tempjurnal"] = "";
                $_SESSION["transaksi"]["jurnal"]["detail"][$x]["detail"]["K"]["dperkir_id"] = $kredit_id[$i]["value"];
                $_SESSION["transaksi"]["jurnal"]["detail"][$x]["detail"]["K"]["kdperkiraan"] = $kredit_kode[$i]["value"];
                $_SESSION["transaksi"]["jurnal"]["detail"][$x]["detail"]["K"]["kdnasabah"] = $kdnasabah_kredit[$i]["value"];
                $_SESSION["transaksi"]["jurnal"]["detail"][$x]["detail"]["K"]["kdsbdaya"] = $kdsbdaya_kredit[$i]["value"];
                $_SESSION["transaksi"]["jurnal"]["detail"][$x]["detail"]["K"]["is_rekanan"] = $is_rekanan_kredit[$i]["value"];
                $_SESSION["transaksi"]["jurnal"]["detail"][$x]["detail"]["K"]["is_sbdaya"] = $is_sbdaya_kredit[$i]["value"];
                $_SESSION["transaksi"]["jurnal"]["detail"][$x]["detail"]["K"]["is_proyek"] = $is_proyek_kredit[$i]["value"];
                $_SESSION["transaksi"]["jurnal"]["detail"][$x]["detail"]["K"]["bukubantu"] = $kredit_bukubantu[$i]["value"];
                $_SESSION["transaksi"]["jurnal"]["detail"][$x]["detail"]["K"]["dk"] = "K";
                $_SESSION["transaksi"]["jurnal"]["detail"][$x]["detail"]["K"]["volume"] = "";
                $_SESSION["transaksi"]["jurnal"]["detail"][$x]["detail"]["K"]["debet"] = 0;
                $_SESSION["transaksi"]["jurnal"]["detail"][$x]["detail"]["K"]["kredit"] = $nilai[$i]["value"];

                $i++;
                $x++;
            }
            //echo "<pre>";
            //print_r($_SESSION["transaksi"]);
            //$data["success"] = "<p>Data Berhasil Di Input</p>";
            $data = $this->addJurnal();
            $json["json"] = $data;
            $this->load->view("template/ajax", $json);
        } else {
            $data['error'] = validation_errors();
            $json['json'] = $data;
            $this->load->view('template/ajax', $json);
        }
    }

    public function test() {
		$this->toolbar->create_toolbar();
        $this->toolbar->cGroupButton();
        $this->toolbar->addLink("", "btn tooltips", base_url()."mod_voucherin", "form_voucherin_list", "cus-application", "List Jurnal", "tooltip", "right");
        $this->toolbar->addLink("", "btn tooltips", base_url()."mod_voucherin/test", "form_voucherin_add", "cus-application-form-add", "Input Jurnal", "tooltip", "right");
        $this->toolbar->addLink("", "btn tooltips", "#", "form_voucherin_delete", "cus-application-form-delete", "Hapus Jurnal", "tooltip", "right");
        $this->toolbar->eGroupButton();
        $data['toolbars'] = $this->toolbar->generate();

        $data['ptitle'] = "Voucher In";
        $data['navs'] = $this->dataset_db->buildNav(0);
        $data['tanggal'] = date("Y-m-d");
        $tabs = $this->session->userdata('tabs');
        if (!$tabs)
            $tabs = array();

        $tabs['mod_voucherin'] = $this->dataset_db->getModule('mod_voucherin');
        $this->session->set_userdata('tabs', $tabs);
        $data['current_tab'] = $tabs['mod_voucherin']['link'];
        $data['content'] = $this->load->view('voucherin_v2', $data, true);
        $this->load->vars($data);
        $this->load->view('default_view');
    }

    public function cek() {
        if (!empty($_SESSION["transaksi"]) AND isset($_SESSION["transaksi"])) {
            echo "<pre><tt>";
            print_r($_SESSION["transaksi"]);
            echo "</tt></pre>";
        }
    }

    public function getSessionId() {
        $this->form_validation->set_rules('id', 'id', 'required');
        if ($this->form_validation->run() == TRUE) {
            if (!empty($_SESSION["transaksi"]) and is_array($_SESSION["transaksi"])) {
                $id = $this->input->post("id");
                $transaksi = $_SESSION["transaksi"];
                //echo "<pre>";
                //print_r($_SESSION["transaksi"]);

                $data['success'] = '<p>Data Berhasil Dibaca</p>';
                $data['tanggal'] = $transaksi["jurnal"]["tanggal"];
                $data['nobukti'] = $transaksi["jurnal"]["nobukti"];
                $data['jenis_transaksi'] = $transaksi["jurnal"]["jenis_transaksi"];
                $data['jenis_jurnal'] = $transaksi["jurnal"]["jenis_jurnal"];
                $data['no_dokumen'] = $transaksi["jurnal"]["no_dokumen"];
                $data['id_proyek'] = $transaksi["jurnal"]["id_proyek"];
                $data['is_approved'] = $transaksi["jurnal"]["is_approved"];
                $data['record'] = $transaksi["jurnal"]["detail"][$id];

                //echo "<pre>";
                //print_r($data['record']);
                $json['json'] = $data;
                $this->load->view('template/ajax', $json);
            } else {
                $data['error'] = '<p>Data Gagal Dibaca</p>';
                $json['json'] = $data;
                $this->load->view('template/ajax', $json);
            }
        } else {
            $data['error'] = '<p>Data Gagal Dibaca</p>';
            $json['json'] = $data;
            $this->load->view('template/ajax', $json);
        }
    }

    public function getSession() {
        //if (!empty($_SESSION["transaksi"]) AND is_array($_SESSION["transaksi"]) AND $_SESSION["transaksi"]["jurnal"]["jenis_jurnal"] == 1) {
        if (!empty($_SESSION["transaksi"]) AND is_array($_SESSION["transaksi"])) {
            $transaksi = $_SESSION["transaksi"];
            //echo "<pre>";
            //print_r($_SESSION["transaksi"]);

            $data['success'] = '<p>Data Berhasil Dibaca</p>';
            $data['tanggal'] = $transaksi["jurnal"]["tanggal"];
            $data['nobukti'] = $transaksi["jurnal"]["nobukti"];
            $data['jenis_transaksi'] = $transaksi["jurnal"]["jenis_transaksi"];
            $data['jenis_jurnal'] = $transaksi["jurnal"]["jenis_jurnal"];
            $data['no_dokumen'] = $transaksi["jurnal"]["no_dokumen"];
            $data['id_proyek'] = $transaksi["jurnal"]["id_proyek"];
            $data['is_approved'] = $transaksi["jurnal"]["is_approved"];
            $i = 0;
            foreach ($transaksi["jurnal"]["detail"] as $key => $val) {
                $i++;
                foreach ($val["detail"] as $k => $v) {
                    if ($k === "K") {
                        $data['kredit'][$i]['keterangan'] = $val["keterangan"];
                        $data['kredit'][$i]['nilai'] = $val["nilai"];
                        $data['kredit'][$i]['dperkir_id'] = $v["dperkir_id"];
                        $data['kredit'][$i]['kdperkiraan'] = $v["kdperkiraan"];
                        $data['kredit'][$i]['is_rekanan'] = $v["is_rekanan"];
                        $data['kredit'][$i]['is_sbdaya'] = $v["is_sbdaya"];
                        $data['kredit'][$i]['bukubantu'] = $v["bukubantu"];
                    }
                }
            }
            $data['kredit_length'] = $i;
            if(array_key_exists('D', $transaksi["jurnal"]["detail"][1]["detail"])){
				$data['debet_id'] = $transaksi["jurnal"]["detail"][1]["detail"]["D"]["dperkir_id"];
				$data['debet_kode'] = $transaksi["jurnal"]["detail"][1]["detail"]["D"]["kdperkiraan"];
				$data['debet_sbdaya'] = $transaksi["jurnal"]["detail"][1]["detail"]["D"]["is_sbdaya"];
				$data['debet_rekanan'] = $transaksi["jurnal"]["detail"][1]["detail"]["D"]["is_rekanan"];
				$data['debet_bukubantu'] = $transaksi["jurnal"]["detail"][1]["detail"]["D"]["bukubantu"];
			}
            //echo "<pre>";
            //print_r($data);
            $json['json'] = $data;
            $this->load->view('template/ajax', $json);
        } else {
            $data['error'] = '<p>Data Gagal Dibaca</p>';
            $json['json'] = $data;
            $this->load->view('template/ajax', $json);
        }
    }

    public function deletejurnal() {
        $this->form_validation->set_rules('id', 'id', 'required');
        if ($this->form_validation->run() == TRUE) {
				$nobukti = $this->input->post('id');
				$this->voucherin_model->deleteJurnal($nobukti);

                unset($_SESSION["transaksi"]);
                /*if (!empty($jurnal) and is_array($jurnal)) {
                    $_SESSION["transaksi"]["jurnal"]["detail"] = $jurnal;
                }*/

                $data['success'] = '<p>Data Berhasil Dihapus</p>';
                $json['json'] = $data;
                $this->load->view('template/ajax', $json);
        } else {
            $data['error'] = '<p>Harap Pilih Data Yang Akan Dihapus ... !</p>';
            $json['json'] = $data;
            $this->load->view('template/ajax', $json);
        }
    }

    public function cekRekanan($username) {
        if ($this->dataset_db->check_username($this->_userconfig["kolom2"], $username)) {
            $this->form_validation->set_message('cekRekanan', "Kode Rekanan " . $username . " Telah Terdaftar, Pilih Yang Lain.");
            return false;
        } else {
            return true;
        }
    }

    public function cleanTransaksi() {
        unset($_SESSION["transaksi"]);
        unset($_SESSION["formulir"]);
    }

    public function addJurnal() {
        if (!empty($_SESSION["transaksi"]) and is_array($_SESSION["transaksi"]) AND $_SESSION["transaksi"]["jurnal"]["jenis_jurnal"] == 1) {

            $transaksi = $_SESSION["transaksi"]["jurnal"];
            $userconfig = $this->dataset_db->getUserconfig($this->session->userdata('ba_user_id'));
            $transaksi_detail = $transaksi["detail"];
            $jurnal = array();
            $jurnal_alokasi = array();
            $jurnal_piutangkso = array();
            $jurnal_hutangkso = array();
			$debet = 0;
			$kredit = 0;
			$dperkir_alokasi = array(557,558,559,560);
			$dperkir_piutangkso = array(113,114,115,116);
			$dperkir_hutangkso = array(485,486,487,488);
			//$dperkir_alokasi = array(560);
			$is_alokasi = false;
			$idproyek_alokasi = '';
			$kdnasabah_alokasi = $this->voucherin_model->getKodeProyek($userconfig["kolom2"]);

            if (!empty($transaksi["nobukti"])) {
                $nobukti = $transaksi["nobukti"];
                $this->voucherin_model->deleteJurnal($nobukti);
            } else {
                $nobukti = $this->voucherin_model->getNobukti($transaksi["tanggal"], $userconfig["kolom2"], $transaksi["jenis_transaksi"]);
            }

            switch ($nobukti) {
                case 'A':
                    $data['error'] = '<p>Periode Accounting Untuk Tanggal Ini Belum Ada</p>';
                    break;
                case 'B':
                    $data['error'] = '<p>Periode Accounting Untuk Tanggal Ini Telah Dikunci</p>';
                    break;

                default:
                    foreach ($transaksi_detail as $key => $value) {
                        $gid = $this->voucherin_model->getGid($userconfig["kolom2"]);
                        foreach ($value["detail"] as $k => $v) {
                            if ($v["dk"] == "D") {
                                $jurnal[] = array(
                                    'tanggal' => $transaksi["tanggal"],
                                    'nobukti' => $nobukti,
                                    'dperkir_id' => $v["dperkir_id"],
                                    'id_proyek' => $userconfig["kolom2"],
                                    'kdnasabah' => $v["kdnasabah"],
                                    'keterangan' => str_replace("'","`",$value["keterangan"]),
                                    'dk' => $v["dk"],
                                    'rupiah' => $value["nilai"],
                                    'create_id' => $this->session->userdata('ba_user_id'),
                                    'create_time' => $this->myauth->timestampIndo(),
                                    'gid' => $gid,
                                    'tempjurnal_jenisjurnal_id' => 1,
                                    'no_dokumen' => $transaksi["no_dokumen"]
                                );
                                $debet = $debet + $value["nilai"];
                            } else {
                                $jurnal[] = array(
                                    'tanggal' => $transaksi["tanggal"],
                                    'nobukti' => $nobukti,
                                    'dperkir_id' => $v["dperkir_id"],
                                    'id_proyek' => $userconfig["kolom2"],
                                    'kdnasabah' => $v["kdnasabah"],
                                    'keterangan' => str_replace("'","`",$value["keterangan"]),
                                    'dk' => $v["dk"],
                                    'rupiah' => $value["nilai"] * -1,
                                    'create_id' => $this->session->userdata('ba_user_id'),
                                    'create_time' => $this->myauth->timestampIndo(),
                                    'gid' => $gid,
                                    'tempjurnal_jenisjurnal_id' => 1,
                                    'no_dokumen' => $transaksi["no_dokumen"]
                                );

                                $kredit = $kredit + $value["nilai"];
                            }
                        }
                    }

                    if (($kredit === $debet)) {
                        //$this->db->insert_batch('tbl_tempjurnal', $jurnal);
                        $insert = $this->voucherin_model->InsertJurnal($jurnal);
                        if ($insert) {
                            $this->cleanTransaksi();
                            $data['success'] = '<p>Jurnal '.$transaksi["no_dokumen"].' berhasil disimpan dengan nomor bukti : '.$nobukti.'</p>';
                        } else {
                            $data['error'] = '<p>Terjadi kesalahan di database</p>';
                        }
                    } else {
                        $data['error'] = '<p>Debet Dan Kredit Tidak Sama</p>';
                    }
                    break;
            }

            //$json['json'] = $data;
            //$this->load->view('template/ajax', $json);
        } else {
            $data['error'] = '<p>Tidak Ada Data Yang Disimpan</p>';
            //$json['json'] = $data;
            //$this->load->view('template/ajax', $json);
        }
        return $data;
    }

	public function list_jurnal() {
		$this->cleanTransaksi();
		$this->toolbar->create_toolbar();
        $this->toolbar->cGroupButton();
        $this->toolbar->addLink("", "btn tooltips", base_url()."mod_voucherin", "form_voucherin_list", "cus-application", "List Jurnal", "tooltip", "right");
        $this->toolbar->addLink("", "btn tooltips", base_url()."mod_voucherin/test", "form_voucherin_add", "cus-application-form-add", "Input Jurnal", "tooltip", "right");
        $this->toolbar->addLink("", "btn tooltips", "#", "form_voucherin_delete", "cus-application-form-delete", "Hapus Jurnal", "tooltip", "right");
        $this->toolbar->eGroupButton();
        $data['toolbars'] = $this->toolbar->generate();

        $DataModel = array(
            array(
                'text' => 'IsApproved',
                'value' => 'text:LOWER(isapprove)',
                'type' => 'custom',
                'callBack' => 'getBoolean',
                'ops' => array("=", "!=")
            ),
            array(
                'text' => 'Periode',
                'value' => 'text:period_id',
                'type' => 'custom',
                'callBack' => 'getperiod',
                'ops' => array("=", "!=")
            ),
            array(
                'text' => 'Tanggal',
                'value' => 'date:date(tanggal)',
                'type' => 'date',
                'callBack' => '',
                'ops' => array("=", "!=", ">", ">=", "<", "<=")
            ),
            array(
                'text' => 'Nomor Bukti',
                'value' => 'text:LOWER(no_dokumen)',
                'type' => 'text',
                'callBack' => '',
                'ops' => array("like", "not like", "=", "!=")
            )
        );

        $defaultvalue = array(
            array(
                'text' => 'Periode',
                'value' => 'text:period_id',
                'defvalue' => $this->site_library->getPeriodeId(date("Y-m-d")),
                'type' => 'custom',
                'callBack' => 'getperiod',
                'ops' => array("=")
            ),
            array(
                'text' => 'IsApproved',
                'value' => 'text:LOWER(isapprove)',
                'defvalue' => 'false',
                'type' => 'custom',
                'callBack' => 'getBoolean',
                'ops' => array("=")
            )
        );

        $data['searchform'] = $this->searchform->setMultiSearch("true")->setDataModel($DataModel)->setDefaultValue($defaultvalue)->genSearchForm();
        $data['ptitle'] = "List Jurnal Voucher In";
        $data['navs'] = $this->dataset_db->buildNav(0);
        $data['tanggal'] = date("Y-m-d");
        $tabs = $this->session->userdata('tabs');
        if (!$tabs)
            $tabs = array();

        $tabs['mod_voucherin'] = $this->dataset_db->getModule('mod_voucherin');
        $this->session->set_userdata('tabs', $tabs);
        $data['current_tab'] = $tabs['mod_voucherin']['link'];
        $data['content'] = $this->load->view('voucherin_v2_list_jurnal', $data, true);
        $this->load->vars($data);
        $this->load->view('default_view');
    }

    public function JurnalToJson() {

        $search = isset($_GET['_search']) ? $_GET['_search'] : 'false';
        $page = $this->input->post('page');
        $limit = $this->input->post('rows');
        $sidx = $this->input->post('sidx');
        $sord = $this->input->post('sord');

        $page = !empty($page) ? $page : 1;
        $limit = !empty($limit) ? $limit : 20;
        $sidx = !empty($sidx) ? $sidx : "no_dokumen";
        $sord = !empty($sord) ? $sord : "asc";
        $userconfig = $this->dataset_db->getUserconfig($this->session->userdata('ba_user_id'));

        if (strtolower($search) == "true") {
            $cols = isset($_GET['cols']) ? $_GET['cols'] : '';
            $ops = isset($_GET['ops']) ? $_GET['ops'] : '';
            $vals = isset($_GET['vals']) ? $_GET['vals'] : '';

            $cari = array();
            for ($x = 0; $x < count($cols); $x++) {
                $cari[$x]['cols'] = $cols[$x];
                $cari[$x]['ops'] = $ops[$x];
                $cari[$x]['vals'] = $vals[$x];
            }
        } else {
            $cari = array(
                array(
                    'cols' => 'text:LOWER(isapprove)',
                    'ops' => '=',
                    'vals' => 'false'
                ),
                array(
                    'cols' => 'text:period_id',
                    'ops' => '=',
                    'vals' => $this->site_library->getPeriodeId(date("Y-m-d"))
                )
            );
        }

        $offset = ($page * $limit) - $limit;
        $offset = ($offset < 0) ? 0 : $offset;

        if (!$sidx)
            $sidx = 1;

        $data = $this->voucherin_model->getJurnal($this->_userconfig["kolom2"], $limit, $offset, $sidx, $sord, $cari, $search);
        $count = $this->voucherin_model->countAll();
        $data2 = $this->voucherin_model->JurnalToJson($data);
        if ($count > 0) {
            $total_pages = ceil($count / $limit);
        } else {
            $total_pages = 0;
        }

        if ($page > $total_pages)
            $page = $total_pages;

        if (!empty($data2) and is_array($data2)) {
            $responce['page'] = $page;
            $responce['total'] = $total_pages;
            $responce['records'] = $count;

            $i = 0;
            foreach ($data2 as $row) {
                $responce['rows'][$i]['id'] = $i;
                $responce['rows'][$i]['cell'] = array(
                    $row['no'],
                    $row['check'],
                    $row['flag'],
                    $row['tanggal'],
                    $row['nomor_bukti'],
                    $row['nomor_dokumen'],
                    $row['kode_proyek'],
                    $row['keterangan'],
                    $row['coa'],
                    $row['rekanan'],
//                    $row['volume'],
                    $row['debet'],
                    $row['kredit'],
                    $row['is_approved']
                );
                $i++;
            }
            echo json_encode($responce);
        } else {
            $responce['page'] = 1;
            $responce['total'] = 1;
            $responce['records'] = 0;
            echo json_encode($responce);
        }
    }

    function tes() {
        echo "<pre>";
        print_r($_SESSION["transaksi"]);
    }

    public function add2formulir() {
    		$id = $this->input->post("id");
    		$nobukti = $this->input->post("nobukti");
    		$tanggal = $this->input->post("tanggal");
    		$jenis = $this->input->post("jenis");
    		$no_dokumen = $this->input->post("no_dokumen");
    		$debet_id = $this->input->post("debet_id");
    		$debet_kode = $this->input->post("debet_kode");
    		$debet_bukubantu = $this->input->post("debet_bukubantu");
    		$kredit_id = $this->input->post("kredit_id");
    		$kredit_kode = $this->input->post("kredit_kode");
    		$kredit_bukubantu = $this->input->post("kredit_bukubantu");
    		$keterangan = $this->input->post("keterangan");
    		$nilai = $this->input->post("nilai");
    		$is_rekanan_debet = $this->input->post("is_rekanan_debet");
    		$is_sbdaya_debet = $this->input->post("is_sbdaya_debet");
    		$is_proyek_debet = $this->input->post("is_proyek_debet");
    		$is_rekanan_kredit = $this->input->post("is_rekanan_kredit");
    		$is_sbdaya_kredit = $this->input->post("is_sbdaya_kredit");
    		$is_proyek_kredit = $this->input->post("is_proyek_kredit");

          //die(print_r($nilai));
          unset($_SESSION["formulir"]);
          if (!isset($_SESSION["formulir"])) {
              $_SESSION["formulir"] = array();
          }

          //if (!empty($_SESSION["transaksi"]["jurnal"]["detail"]) AND is_array($_SESSION["transaksi"]["jurnal"]["detail"])) {
          //$x = count($_SESSION["transaksi"]["jurnal"]["detail"]) + 1;
          if (!empty($id)) {
              $x = $id;
          } else {
              $x = 1;
          }

          if (strtolower($is_rekanan_debet) == "t") {
              $kdnasabah_debet = $debet_bukubantu;
              $kdsbdaya_debet = "";
          } elseif (strtolower($is_sbdaya_debet) == "t") {
              $kdnasabah_debet = "";
              $kdsbdaya_debet = $debet_bukubantu;
          } elseif (strtolower($is_proyek_debet) == "t") {
              $kdnasabah_debet = $debet_bukubantu;
              $kdsbdaya_debet = "";
          } else {
              $kdnasabah_debet = "";
              $kdsbdaya_debet = "";
          }

          $_SESSION["formulir"]["jurnal"]["tanggal"] = $tanggal;
          $_SESSION["formulir"]["jurnal"]["no_dokumen"] = $no_dokumen;
          $_SESSION["formulir"]["jurnal"]["jenis_transaksi"] = $jenis;
          $_SESSION["formulir"]["jurnal"]["jenis_jurnal"] = 1;
          $_SESSION["formulir"]["jurnal"]["nobukti"] = $nobukti;
          $_SESSION["formulir"]["jurnal"]["id_proyek"] = "";
          $_SESSION["formulir"]["jurnal"]["is_approved"] = "";
          $i = 0;
          foreach ($kredit_id as $val) {

              if (strtolower($is_rekanan_kredit[$i]["value"]) == "t") {
                  $kdnasabah_kredit[$i]["value"] = $kredit_bukubantu[$i]["value"];
                  $kdsbdaya_kredit[$i]["value"] = "";
              } elseif (strtolower($is_sbdaya_kredit[$i]["value"]) == "t") {
                  $kdnasabah_kredit[$i]["value"] = "";
                  $kdsbdaya_kredit[$i]["value"] = $kredit_bukubantu[$i]["value"];
              } elseif (strtolower($is_proyek_kredit[$i]["value"]) == "t") {
                  $kdnasabah_kredit[$i]["value"] = $kredit_bukubantu[$i]["value"];
                  $kdsbdaya_kredit[$i]["value"] = "";
              } else {
                  $kdnasabah_kredit[$i]["value"] = "";
                  $kdsbdaya_kredit[$i]["value"] = "";
              }

              $_SESSION["formulir"]["jurnal"]["detail"][$x]["gid"] = "";
              $_SESSION["formulir"]["jurnal"]["detail"][$x]["keterangan"] = $keterangan[$i]["value"];
              $_SESSION["formulir"]["jurnal"]["detail"][$x]["nilai"] = $nilai[$i]["value"];

              $_SESSION["formulir"]["jurnal"]["detail"][$x]["detail"]["D"]["id_tempjurnal"] = "";
              $_SESSION["formulir"]["jurnal"]["detail"][$x]["detail"]["D"]["dperkir_id"] = $debet_id;
              $_SESSION["formulir"]["jurnal"]["detail"][$x]["detail"]["D"]["kdperkiraan"] = $debet_kode;
              $_SESSION["formulir"]["jurnal"]["detail"][$x]["detail"]["D"]["kdnasabah"] = $kdnasabah_debet;
              $_SESSION["formulir"]["jurnal"]["detail"][$x]["detail"]["D"]["kdsbdaya"] = $kdsbdaya_debet;
              $_SESSION["formulir"]["jurnal"]["detail"][$x]["detail"]["D"]["is_rekanan"] = $is_rekanan_debet;
              $_SESSION["formulir"]["jurnal"]["detail"][$x]["detail"]["D"]["is_sbdaya"] = $is_sbdaya_debet;
              $_SESSION["formulir"]["jurnal"]["detail"][$x]["detail"]["D"]["is_proyek"] = $is_proyek_debet;
              $_SESSION["formulir"]["jurnal"]["detail"][$x]["detail"]["D"]["bukubantu"] = $debet_bukubantu;
              $_SESSION["formulir"]["jurnal"]["detail"][$x]["detail"]["D"]["dk"] = "D";
              $_SESSION["formulir"]["jurnal"]["detail"][$x]["detail"]["D"]["volume"] = "";
              $_SESSION["formulir"]["jurnal"]["detail"][$x]["detail"]["D"]["debet"] = $nilai[$i]["value"];
              $_SESSION["formulir"]["jurnal"]["detail"][$x]["detail"]["D"]["kredit"] = 0;

              $_SESSION["formulir"]["jurnal"]["detail"][$x]["detail"]["K"]["id_tempjurnal"] = "";
              $_SESSION["formulir"]["jurnal"]["detail"][$x]["detail"]["K"]["dperkir_id"] = $kredit_id[$i]["value"];
              $_SESSION["formulir"]["jurnal"]["detail"][$x]["detail"]["K"]["kdperkiraan"] = $kredit_kode[$i]["value"];
              $_SESSION["formulir"]["jurnal"]["detail"][$x]["detail"]["K"]["kdnasabah"] = $kdnasabah_kredit[$i]["value"];
              $_SESSION["formulir"]["jurnal"]["detail"][$x]["detail"]["K"]["kdsbdaya"] = $kdsbdaya_kredit[$i]["value"];
              $_SESSION["formulir"]["jurnal"]["detail"][$x]["detail"]["K"]["is_rekanan"] = $is_rekanan_kredit[$i]["value"];
              $_SESSION["formulir"]["jurnal"]["detail"][$x]["detail"]["K"]["is_sbdaya"] = $is_sbdaya_kredit[$i]["value"];
              $_SESSION["formulir"]["jurnal"]["detail"][$x]["detail"]["K"]["is_proyek"] = $is_proyek_kredit[$i]["value"];
              $_SESSION["formulir"]["jurnal"]["detail"][$x]["detail"]["K"]["bukubantu"] = $kredit_bukubantu[$i]["value"];
              $_SESSION["formulir"]["jurnal"]["detail"][$x]["detail"]["K"]["dk"] = "K";
              $_SESSION["formulir"]["jurnal"]["detail"][$x]["detail"]["K"]["volume"] = "";
              $_SESSION["formulir"]["jurnal"]["detail"][$x]["detail"]["K"]["debet"] = 0;
              $_SESSION["formulir"]["jurnal"]["detail"][$x]["detail"]["K"]["kredit"] = $nilai[$i]["value"];

              $i++;
              $x++;
          }
    }

    public function showFormulir() {
        $data['formulir'] = $_SESSION["formulir"];
        $data['content'] = $this->load->view('popup_formulir', $data, true);
        $this->load->vars($data);
        $this->load->view('default_picker');
    }
}
