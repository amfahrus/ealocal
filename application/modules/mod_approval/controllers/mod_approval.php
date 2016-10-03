<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class mod_approval extends CI_Controller {

    protected $_userconfig = array();

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
        $this->load->model('approval_model');
        $this->load->model('dataset_db');
//        $this->load->library('search_form');
        $this->load->library("searchform");

        $this->_userconfig = $this->dataset_db->getUserconfig($this->session->userdata('ba_user_id'));
    }

    public function index() {

        $this->toolbar->create_toolbar();
        $this->toolbar->cGroupButton();
        $this->toolbar->addButton("", "btn tooltips", "button", "form_approve_jurnal", "form_approve_jurnal", "cus-page-save", "Approv Jurnal", "tooltip", "right");
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
        $data['ptitle'] = "Jurnal Approve";
        $data['level'] = $this->session->userdata('ba_unit_kerja');
        $data['unitkerja'] = $this->dataset_db->getSubUnitkerja();
        $data['navs'] = $this->dataset_db->buildNav(0);
//        $data['getbulan'] = $this->search_form->getBulan();
//        $data['gettahun'] = $this->search_form->getTahun();
        $tabs = $this->session->userdata('tabs');
        if (!$tabs)
            $tabs = array();
        $tabs['mod_approval'] = $this->dataset_db->getModule('mod_approval');
        $this->session->set_userdata('tabs', $tabs);
        $data['current_tab'] = $tabs['mod_approval']['link'];
        $data['content'] = $this->load->view('approval_form', $data, true);
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

        $data = $this->approval_model->getJurnal($this->_userconfig["kolom2"], $limit, $offset, $sidx, $sord, $cari, $search);
        $count = $this->approval_model->countAll();
        $data2 = $this->approval_model->JurnalToJson($data);
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

    public function addJurnal() {
        $this->form_validation->set_rules('jq_checkbox_added', 'Jurnal Approve', 'required');
        if ($this->form_validation->run() == TRUE) {
            if (!empty($_POST['jq_checkbox_added'])) {
                $savejurnal = array();
                $nomor_bukti = $this->input->post('jq_checkbox_added');
                $userconfig = $this->dataset_db->getUserconfig($this->session->userdata('ba_user_id'));
                $savejurnal = $this->approval_model->ApprovJurnal($userconfig["kolom2"], $this->session->userdata('ba_user_id'), $nomor_bukti);


                $temp_log = array();
                $error_counter = 0;
                $no = 1;

                if (!empty($savejurnal) and is_array($savejurnal)) {
                    foreach ($savejurnal as $value) {
                        if (strtolower($value["status"]) == "gagal") {
                            $error_counter++;
                        }

                        $temp_log[] = array(
                            "no" => $no,
                            "no_dokumen" => $value["no_dokumen"],
                            "nobukti" => $value["nobukti"],
                            "status" => $value["status"],
                            "keterangan" => $value["keterangan"]
                        );

                        $no++;
                    }
                }

                $data["response"]["informasi"] = $temp_log;
                $data["response"]["counter_error"] = $error_counter;
                $data["response"]['success'] = '<p>Data Berhasil Di Simpan</p>';
                $json['json'] = $data;
                $this->load->view('template/ajax', $json);
            } else {
                $data["response"]['error'] = '<p>Tidak ada data yang di simpan</p>';
                $json['json'] = $data;
                $this->load->view('template/ajax', $json);
            }
        } else {
            $data["response"]["error"] = validation_errors();
            $json['json'] = $data;
            $this->load->view('template/ajax', $json);
        }
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
            
            $this->approval_model->get_nobukti($nobukti);
            $jurnal = $this->approval_model->getArrayNobukti();

            $_SESSION["transaksi"] = $jurnal;

            $data['success'] = "<p>Data Berhasil</p>";
            if($_SESSION["transaksi"]["jurnal"]["jenis_jurnal"] == 1){
				$data['redirect'] = base_url() . 'mod_voucherin';
            } elseif($_SESSION["transaksi"]["jurnal"]["jenis_jurnal"] == 2){
				$data['redirect'] = base_url() . 'mod_voucherout';
			} else {
				$data['redirect'] = base_url() . 'mod_vouchermem';
			}
            $json['json'] = $data;
            $this->load->view('template/ajax', $json);
        } else {
            $data['error'] = validation_errors();
            $json['json'] = $data;
            $this->load->view('template/ajax', $json);
        }
    }
	
	public function setVoucher($id) {
		$nobukti = $this->approval_model->getNobukti($id);
		$data['nobukti'] = $nobukti;
        $data['content'] = $this->load->view('popup_setvoucher', $data, true);
        $this->load->vars($data);
        $this->load->view('default_picker');
    }
    
    public function act_setvoucher() {
        $this->form_validation->set_rules("nobukti", "Nomor Bukti", "required");
        $this->form_validation->set_rules("jenis_voucher", "Jenis Voucher", "required");

        if ($this->form_validation->run() == TRUE) {

            $nobukti = $this->input->post("nobukti");
            $tempjurnal_jenisjurnal_id = $this->input->post("jenis_voucher");
			$jurnaldata["tempjurnal_jenisjurnal_id"] = $tempjurnal_jenisjurnal_id;
            $this->approval_model->edit_tempjurnal($jurnaldata, $nobukti);
            $data['success'] = "<p>Data Berhasil Di Simpan</p>";
            $data['nobukti'] = $nobukti;
			$json['json'] = $data;
			$this->load->view('template/ajax', $json);
        } else {
            $data['error'] = validation_errors();
            $json['json'] = $data;
            $this->load->view('template/ajax', $json);
        }
    }
}
