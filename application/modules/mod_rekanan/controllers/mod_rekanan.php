<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class mod_rekanan extends CI_Controller {

    private $_userconfig = array();

    public function __construct() {
        parent::__construct();
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
        $this->load->model('rekanan_model');
        $this->load->model('dataset_db');
        $this->load->library("searchform");
        $this->_userconfig = $this->dataset_db->getUserconfig($this->session->userdata('ba_user_id'));
    }

    public function index() {
        $this->toolbar->create_toolbar();
        $this->toolbar->cGroupButton();
        $this->toolbar->addLink("", "btn tooltips", base_url() . "mod_rekanan", "form_rekanan_list", "cus-application", "List Data User", "tooltip", "right");
        $this->toolbar->addLink("", "btn tooltips", base_url() . "mod_rekanan/form_rekanan_add", "form_rekanan_new", "cus-application-form-add", "Tambah User", "tooltip", "right");
        $this->toolbar->addLink("", "btn tooltips", "#", "form_rekanan_delete", "cus-application-form-delete", "Delete User", "tooltip", "right");
        $this->toolbar->eGroupButton();
//        $this->toolbar->cGroupButton();
//        $this->toolbar->addLink("", "btn tooltips", base_url() . "mod_rekanan/from_excel", "form_rekanan_import_xls", "cus-page-white-excel", "Import Data Excel", "tooltip", "right");
//        $this->toolbar->addLink("", "btn tooltips", base_url() . "mod_rekanan/to_excel", "form_rekanan_export_xls", "cus-page-excel", "Eksport Data Excel", "tooltip", "right");
//        $this->toolbar->eGroupButton();
        $data['toolbars'] = $this->toolbar->generate();

        $DataModel = array(
            array(
                'text' => 'Proyek',
                'value' => 'text:LOWER(nama_proyek)',
                'type' => 'text',
                'callBack' => '',
                'ops' => array("like", "not like", "=", "!=")
            ),
            array(
                'text' => 'Tipe',
                'value' => 'text:LOWER(type)',
                'type' => 'text',
                'callBack' => '',
                'ops' => array("like", "not like", "=", "!=")
            ),
            array(
                'text' => 'Kode Rekanan',
                'value' => 'text:LOWER(kode_rekanan)',
                'type' => 'text',
                'callBack' => '',
                'ops' => array("like", "not like", "=", "!=")
            ),
            array(
                'text' => 'Nama Rekanan',
                'value' => 'text:LOWER(nama_rekanan)',
                'type' => 'text',
                'callBack' => '',
                'ops' => array("like", "not like", "=", "!=")
            )
        );

        $defaultvalue = array();

        $data['searchform'] = $this->searchform->setMultiSearch("true")->setDataModel($DataModel)->setDefaultValue($defaultvalue)->genSearchForm();
        $data['ptitle'] = "Master Rekanan";
        $data['navs'] = $this->dataset_db->buildNav(0);
        $tabs = $this->session->userdata('tabs');
        if (!$tabs)
            $tabs = array();
        $tabs['mod_rekanan'] = $this->dataset_db->getModule('mod_rekanan');
        $this->session->set_userdata('tabs', $tabs);
        $data['current_tab'] = $tabs['mod_rekanan']['link'];
        $data['content'] = $this->load->view('rekanan_list', $data, true);
        $this->load->vars($data);
        $this->load->view('default_view');
    }

    public function rekanan_json() {
        $userconfig = $this->dataset_db->getUserconfig($this->session->userdata('ba_user_id'));
        $search = isset($_GET['_search']) ? $_GET['_search'] : 'false';
        $page = $this->input->post('page');
        $limit = $this->input->post('rows');
        $sidx = $this->input->post('sidx');
        $sord = $this->input->post('sord');

        $page = !empty($page) ? $page : 1;
        $limit = !empty($limit) ? $limit : 10;
        $sidx = !empty($sidx) ? $sidx : "nama_rekanan";
        $sord = !empty($sord) ? $sord : "asc";

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
            $cari = "";
        }

        $offset = ($page * $limit) - $limit;
        $offset = ($offset < 0) ? 0 : $offset;

        if (!$sidx)
            $sidx = 1;
        $query = $this->rekanan_model->getAll($limit, $offset, $sidx, $sord, $cari, $search, $userconfig["kolom2"]);
        $count = $this->rekanan_model->countAll();

        if ($count > 0) {
            $total_pages = ceil($count / $limit);
        } else {
            $total_pages = 0;
        }

        if ($page > $total_pages)
            $page = $total_pages;

        $responce['page'] = $page;
        $responce['total'] = $total_pages;
        $responce['records'] = $count;
        $i = 0;
        foreach ($query as $row) {
            $responce['rows'][$i]['id'] = $row['id_rekanan'];
            $responce['rows'][$i]['cell'] = array($row['id_rekanan'], $row['kode_proyek'], $row['type'], $row['kode_rekanan'], $row['nama_rekanan'], '<a class="link_edit" href="' . base_url() . 'mod_rekanan/rekanan_view/' . $row['id_rekanan'] . '"><img src="' . base_url() . 'media/edit.png" /></a>');
            $i++;
        }
        echo json_encode($responce);
    }

    public function popup_json($coa = 0) {
        $search = isset($_GET['_search']) ? $_GET['_search'] : 'false';
        $page = $this->input->post('page');
        $limit = $this->input->post('rows');
        $sidx = $this->input->post('sidx');
        $sord = $this->input->post('sord');

        $page = !empty($page) ? $page : 1;
        $limit = !empty($limit) ? $limit : 10;
        $sidx = !empty($sidx) ? $sidx : "nama_rekanan";
        $sord = !empty($sord) ? $sord : "asc";


        if (strtolower($search) == "true") {
            $kode_rekanan = isset($_GET['kode_rekanan']) ? $_GET['kode_rekanan'] : '';
            $nama_rekanan = isset($_GET['nama_rekanan']) ? $_GET['nama_rekanan'] : '';
            $cari = array();
            $cari["kode_rekanan"] = $kode_rekanan;
            $cari["nama_rekanan"] = $nama_rekanan;
        } else {
            $cari = "";
        }

        $offset = ($page * $limit) - $limit;
        $offset = ($offset < 0) ? 0 : $offset;

        if (!$sidx)
            $sidx = 1;
		
        $query = $this->rekanan_model->PopupGetAll($limit, $offset, $sidx, $sord, $cari, $search, $this->_userconfig["kolom2"], $coa);
        $count = $this->rekanan_model->PopupCountAll();

        if ($count > 0) {
            $total_pages = ceil($count / $limit);
        } else {
            $total_pages = 0;
        }

        if ($page > $total_pages)
            $page = $total_pages;

        $responce['page'] = $page;
        $responce['total'] = $total_pages;
        $responce['records'] = $count;
        $i = 0;
        foreach ($query as $row) {
            $responce['rows'][$i]['id'] = $row["id_rekanan"];
            $responce['rows'][$i]['cell'] = array("", $row["id_rekanan"], $row["kode_rekanan"], $row["nama_rekanan"]);
            $i++;
        }
        echo json_encode($responce);
    }

    public function popup_rekanan() {
        $data['coa'] = !empty($_GET['coa']) ? $_GET['coa'] : false;
        $data['content'] = $this->load->view('popup_rekanan', $data, true);
        $this->load->vars($data);
        $this->load->view('default_picker');
    }

    public function form_rekanan_add() {
        $this->toolbar->create_toolbar();
        $this->toolbar->cGroupButton();
        $this->toolbar->addLink("", "btn tooltips", base_url() . "mod_rekanan", "form_rekanan_list", "cus-application", "List Data User", "tooltip", "right");
        $this->toolbar->addLink("", "btn tooltips", base_url() . "mod_rekanan/form_rekanan_add", "form_rekanan_new", "cus-application-form-add", "Tambah User", "tooltip", "right");
        $this->toolbar->addLink("", "btn tooltips", "#", "form_rekanan_delete", "cus-application-form-delete", "Delete User", "tooltip", "right");
        $this->toolbar->eGroupButton();
//        $this->toolbar->cGroupButton();
//        $this->toolbar->addLink("", "btn tooltips", base_url() . "mod_rekanan/from_excel", "form_rekanan_import_xls", "cus-page-white-excel", "Import Data Excel", "tooltip", "right");
//        $this->toolbar->addLink("", "btn tooltips", base_url() . "mod_rekanan/to_excel", "form_rekanan_export_xls", "cus-page-excel", "Eksport Data Excel", "tooltip", "right");
//        $this->toolbar->eGroupButton();
        $data['toolbars'] = $this->toolbar->generate();
		$userconfig = $this->dataset_db->getUserconfig($this->session->userdata('ba_user_id'));
        $data["kode_perkiraan"] = $this->site_library->getKodePerkiraan($userconfig["kolom2"]);
        $data['ptitle'] = "Master Rekanan";
        $data['navs'] = $this->dataset_db->buildNav(0);
        $tabs = $this->session->userdata('tabs');
        if (!$tabs)
            $tabs = array();
        $tabs['mod_rekanan'] = $this->dataset_db->getModule('mod_rekanan');
        $this->session->set_userdata('tabs', $tabs);
        $data['unitkerja'] = $this->dataset_db->getSubUnitkerja();
        $data['kode_proyek'] = $this->dataset_db->getDataProyek();
//            $data['kode_proyek'] = $this->rekanan_model->getKodeProyek();
        $data['type_rekanan'] = $this->rekanan_model->getTypeRekanan();
        $data['current_tab'] = $tabs['mod_rekanan']['link'];
        $data['content'] = $this->load->view('rekanan_add', $data, true);
        $this->load->vars($data);
        $this->load->view('default_view');
    }

    public function rekanan_add() {
        $this->form_validation->set_rules("kode_rekanan", "Kode Rekanan", "required|xss_clean|callback_checkKodeRekanan");
        $this->form_validation->set_rules("nama_rekanan", "Nama Rekanan", "required|xss_clean");
        //$this->form_validation->set_rules("nama_kontak", "Nama Kontak", "required|xss_clean");
        //$this->form_validation->set_rules("alamat", "Alamat", "xss_clean");
        //$this->form_validation->set_rules("kota", "Kota", "xss_clean");
        //$this->form_validation->set_rules("telp_rekanan", "Telp Rekanan", "xss_clean");
        //$this->form_validation->set_rules("telp_kontak", "Telp Kontak", "xss_clean");
        $this->form_validation->set_rules("type_rekanan", "Type Rekanan", "required|xss_clean");
        $this->form_validation->set_rules("kode_perkiraan", "Kode Perkiraan", "required|xss_clean");

        if ($this->form_validation->run() == TRUE) {
            $userconfig = $this->dataset_db->getUserconfig($this->session->userdata('ba_user_id'));
            $kode_perkiraan = $this->input->post('kode_perkiraan');

            $field["kode_rekanan"] = $this->input->post("kode_rekanan");
            $field["nama_rekanan"] = $this->input->post("nama_rekanan");
            $field["nama_kontak"] = $this->input->post("nama_kontak");
            $field["alamat"] = $this->input->post("alamat");
            $field["kota"] = $this->input->post("kota");
            $field["telp_rekanan"] = $this->input->post("telp_rekanan");
            $field["telp_kontak"] = $this->input->post("telp_kontak");
            $field["type_rekanan"] = $this->input->post("type_rekanan");
            $field["id_proyek"] = $userconfig["kolom2"];
            $field["create_id"] = $this->session->userdata('ba_user_id');
            $field["create_time"] = $this->myauth->timestampIndo();

            $insert = $this->rekanan_model->insert($field);

            $temp_result = array();
           
			foreach ($kode_perkiraan as $value) {
				$temp_result[] = array(
					'bukubantu_id_proyek' => $userconfig["kolom2"],
					'bukubantu_dperkir_id' => $value,
					'bukubantu_kdrekanan' => $insert,
					'bukubantu_isrekanan' => 't',
					'bukubantu_issbdaya' => 'f',
					'bukubantu_isproyek' => 'f'
				);
			}

            if ($insert) {

                $this->rekanan_model->insert_bukubantu($temp_result);

                $data['success'] = '<p>Data Berhasil Disimpan</p>';
                $json['json'] = $data;
                $this->load->view('template/ajax', $json);
            } else {
                $data['error'] = '<p>Data Gagal Disimpan</p>';
                $json['json'] = $data;
                $this->load->view('template/ajax', $json);
            }
        } else {
            $data['error'] = validation_errors();
            $json['json'] = $data;
            $this->load->view('template/ajax', $json);
        }
    }

    public function getDataProyek() {
        $this->form_validation->set_rules("id", "id", "required|xss_clean");
        $this->form_validation->set_rules("id_proyek", "id_proyek", "required|xss_clean");
        if ($this->form_validation->run() == TRUE) {
            $id = $this->input->post("id");
            $id_proyek = $this->input->post("id_proyek");
            $proyek = $this->dataset_db->getDataProyek($id);
            foreach ($proyek as $key => $value) {
                if ($id_proyek == $key) {
                    echo "<option value=\"" . $key . "\" selected>" . $value . "</option>";
                } else {
                    echo "<option value=\"" . $key . "\">" . $value . "</option>";
                }
            }
        }
    }

    public function getRekanan() {
        $dt['kode_rekanan'] = $this->input->post('item');
        $json['json'] = $this->rekanan_model->getPicker($dt);
        $this->load->view('template/ajax', $json);
    }

    public function checkKodeRekanan() {
        $kode_rekanan = $this->input->post("kode_rekanan");
        $userconfig = $this->dataset_db->getUserconfig($this->session->userdata('ba_user_id'));
        if ($this->rekanan_model->checkKodeRekanan($kode_rekanan, $userconfig["kolom2"])) {
            $this->form_validation->set_message('checkKodeRekanan', "Kode Rekanan " . $kode_rekanan . " Telah Terdaftar, Pilih Yang Lain.");
            return FALSE;
        } else {
            return TRUE;
        }
    }

    public function rekanan_delete() {
        $id = $this->input->post('id');
        $this->rekanan_model->delete($id);
    }

    public function rekanan_view($id = false) {
        try {
            if ($id) {
                $check_id = $this->rekanan_model->cekId($id);
                if ($check_id) {
                    $this->toolbar->create_toolbar();
                    $this->toolbar->cGroupButton();
                    $this->toolbar->addLink("", "btn tooltips", base_url() . "mod_rekanan", "form_rekanan_list", "cus-application", "List Data User", "tooltip", "right");
                    $this->toolbar->addLink("", "btn tooltips", base_url() . "mod_rekanan/form_rekanan_add", "form_rekanan_new", "cus-application-form-add", "Tambah User", "tooltip", "right");
                    $this->toolbar->addLink("", "btn tooltips", "#", "form_rekanan_delete", "cus-application-form-delete", "Delete User", "tooltip", "right");
                    $this->toolbar->eGroupButton();
                    $this->toolbar->cGroupButton();
                    $this->toolbar->addLink("", "btn tooltips", base_url() . "mod_rekanan/from_excel", "form_rekanan_import_xls", "cus-page-white-excel", "Import Data Excel", "tooltip", "right");
                    $this->toolbar->addLink("", "btn tooltips", base_url() . "mod_rekanan/to_excel", "form_rekanan_export_xls", "cus-page-excel", "Eksport Data Excel", "tooltip", "right");
                    $this->toolbar->eGroupButton();
                    $data['toolbars'] = $this->toolbar->generate();

                    $data["kode_perkiraan"] = $this->site_library->getKodePerkiraan($this->_userconfig["kolom2"]);
                    $data["selected"] = $this->site_library->getSelectedKodePerkiraan($id, $this->_userconfig["kolom2"]);
                    $data['ptitle'] = "Master Rekanan";
                    $data['navs'] = $this->dataset_db->buildNav(0);
                    $tabs = $this->session->userdata('tabs');
                    if (!$tabs)
                        $tabs = array();
                    $tabs['mod_rekanan'] = $this->dataset_db->getModule('mod_rekanan');
                    $this->session->set_userdata('tabs', $tabs);
                    $data['detail'] = $this->rekanan_model->get($id);
                    $data['unitkerja'] = $this->dataset_db->getSubUnitkerja();
                    $data['kode_proyek'] = $this->dataset_db->getDataProyek();
                    $data['type_rekanan'] = $this->rekanan_model->getTypeRekanan();
                    $data['current_tab'] = $tabs['mod_rekanan']['link'];
                    $data['content'] = $this->load->view('rekanan_edit', $data, true);
                    $this->load->vars($data);
                    $this->load->view('default_view');
                } else {
                    throw new Exception('Error');
                }
            } else {
                throw new Exception('Error');
            }
        } catch (Exception $ex) {
            redirect('forbidden');
        }
    }

    public function rekanan_edit() {
        $this->form_validation->set_rules("kode_rekanan", "Kode Rekanan", "required|xss_clean");
        $this->form_validation->set_rules("nama_rekanan", "Nama Rekanan", "required|xss_clean");
        $this->form_validation->set_rules("nama_kontak", "Nama Kontak", "required|xss_clean");
        $this->form_validation->set_rules("alamat", "Alamat", "xss_clean");
        $this->form_validation->set_rules("kota", "Kota", "xss_clean");
        $this->form_validation->set_rules("telp_rekanan", "Telp Rekanan", "xss_clean");
        $this->form_validation->set_rules("telp_kontak", "Telp Kontak", "xss_clean");
        $this->form_validation->set_rules("type_rekanan", "Type Rekanan", "required|xss_clean");
        $this->form_validation->set_rules("kode_perkiraan", "Kode Perkiraan", "required|xss_clean");

        if ($this->form_validation->run() == TRUE) {
            $userconfig = $this->dataset_db->getUserconfig($this->session->userdata('ba_user_id'));
            $kode_perkiraan = $this->input->post('kode_perkiraan');
            $id = $this->input->post("id");
            $field["kode_rekanan"] = $this->input->post("kode_rekanan");
            $field["nama_rekanan"] = $this->input->post("nama_rekanan");
            $field["nama_kontak"] = $this->input->post("nama_kontak");
            $field["alamat"] = $this->input->post("alamat");
            $field["kota"] = $this->input->post("kota");
            $field["telp_rekanan"] = $this->input->post("telp_rekanan");
            $field["telp_kontak"] = $this->input->post("telp_kontak");
            $field["type_rekanan"] = $this->input->post("type_rekanan");
            $field["id_proyek"] = $userconfig["kolom2"];
            $field["modify_id"] = $this->session->userdata('ba_user_id');
            $field["modify_time"] = $this->myauth->timestampIndo();

            $update = $this->rekanan_model->update($field, $id);

            $temp_result = array();
            //die(print_r($kode_perkiraan));
            foreach ($kode_perkiraan as $value) {
                $temp_result[] = array(
                    'bukubantu_id_proyek' => $userconfig["kolom2"],
                    'bukubantu_dperkir_id' => $value,
                    'bukubantu_kdrekanan' => $update,
                    'bukubantu_isrekanan' => 't',
                    'bukubantu_issbdaya' => 'f'
                );
            }

            if ($update) {
                $this->rekanan_model->update_bukubantu($temp_result, $update);
                $data['success'] = '<p>Data Berhasil Disimpan</p>';
                $data['redirect'] = base_url() . 'mod_rekanan';
                $json['json'] = $data;
                $this->load->view('template/ajax', $json);
            } else {
                $data['error'] = '<p>Data Gagal Disimpan</p>';
                $json['json'] = $data;
                $this->load->view('template/ajax', $json);
            }
        } else {
            $data['error'] = validation_errors();
            $json['json'] = $data;
            $this->load->view('template/ajax', $json);
        }
    }

    public function to_excel() {
        $this->load->library('excel');
        $header = array('No', 'Kode Perusahaan', 'Nama Perusahaan', 'Telp. Perusahaan', 'Nama Kontak', 'Alamat', 'Telp. Kontak', 'Kota', 'Tipe');
        $result = array();
        $cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_to_discISAM;
        $cacheSettings = array('dir' => '/var/www/tmp');
        PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
        //$inputFileType = 'Excel2007';
        //$objReader = PHPExcel_IOFactory::createReader($inputFileType);
        //$objReader->setReadDataOnly(true);
        $database = $this->rekanan_model->getAllForExcel();
        if (count($database) > 0) {
            $result = $database;
        }
        $this->excel->getProperties()->setTitle("Master_Rekanan");
        $this->excel->getProperties()->setSubject("Master_Rekanan");
        $this->excel->getProperties()->setDescription("Master_Rekanan");
        $this->excel->getProperties()->setKeywords("Master_Rekanan");
        $this->excel->getProperties()->setCategory("Master_Rekanan");

        $this->excel->getActiveSheet()->fromArray($header, null, 'A1');
        $this->excel->getActiveSheet()->fromArray($result, null, 'A2');
        $this->excel->setActiveSheetIndex(0);
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');

        header('Content-type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="Master_Rekanan"');
        $objWriter->save("php://output");
    }

    public function popup_add() {
		$userconfig = $this->dataset_db->getUserconfig($this->session->userdata('ba_user_id'));
        $data['unitkerja'] = $this->dataset_db->getSubUnitkerja();
        $data['kode_proyek'] = $this->dataset_db->getDataProyek();
        $data['type_rekanan'] = $this->rekanan_model->getTypeRekanan();
        $data["kode_perkiraan"] = $this->site_library->getKodePerkiraan($userconfig["kolom2"]);
        $data["kode_perkiraan2"] = array();
        $data['content'] = $this->load->view('popup_add', $data, true);
        $this->load->vars($data);
        $this->load->view('default_picker');
    }

    public function popup_edit() {
		$userconfig = $this->dataset_db->getUserconfig($this->session->userdata('ba_user_id'));
        $data['unitkerja'] = $this->dataset_db->getSubUnitkerja();
        $data['kode_proyek'] = $this->dataset_db->getDataProyek();
        $data['type_rekanan'] = $this->rekanan_model->getTypeRekanan();
        $data["kode_perkiraan"] = $this->site_library->getKodePerkiraan($userconfig["kolom2"]);
        $data["kode_perkiraan2"] = array();
        $data['content'] = $this->load->view('popup_edit', $data, true);
        $this->load->vars($data);
        $this->load->view('default_picker');
    }

    public function popup_view($id = false) {
        try {
            if ($id) {
                if (isInteger($id)) {
					$userconfig = $this->dataset_db->getUserconfig($this->session->userdata('ba_user_id'));
                    $data["kode_perkiraan"] = $this->site_library->getKodePerkiraan($userconfig["kolom2"]);
                    $data["selected"] = $this->site_library->getSelectedKodePerkiraan($id, $this->_userconfig["kolom2"]);
                    $data['detail'] = $this->rekanan_model->get($id);
                    $data['unitkerja'] = $this->dataset_db->getSubUnitkerja();
                    $data['kode_proyek'] = $this->dataset_db->getDataProyek();
                    $data['type_rekanan'] = $this->rekanan_model->getTypeRekanan();
                    $data['content'] = $this->load->view('popup_edit', $data, true);
                    $this->load->vars($data);
                    $this->load->view('default_picker');
                } else {
                    throw new Exception('Error');
                }
            } else {
                throw new Exception('Error');
            }
        } catch (Exception $ex) {
            redirect('forbidden');
        }
    }

    public function get_rekanan() {
        $dt['id_rekanan'] = $this->input->post('item');
        $json['json'] = $this->rekanan_model->getPicker($dt);
        $this->load->view('template/ajax', $json);
    }

    public function popup_rekanan_add() {
        $this->form_validation->set_rules("unitkerja", "unitkerja", "required|xss_clean");
        $this->form_validation->set_rules("kode_rekanan", "kode_rekanan", "required|xss_clean|callback_checkKodeRekanan");
        $this->form_validation->set_rules("nama_rekanan", "nama_rekanan", "required|xss_clean");
        $this->form_validation->set_rules("nama_kontak", "nama_kontak", "required|xss_clean");
        $this->form_validation->set_rules("alamat", "alamat", "xss_clean");
        $this->form_validation->set_rules("kota", "kota", "xss_clean");
        $this->form_validation->set_rules("telp_rekanan", "telp_rekanan", "xss_clean");
        $this->form_validation->set_rules("telp_kontak", "telp_kontak", "xss_clean");
        $this->form_validation->set_rules("type_rekanan", "type_rekanan", "required|xss_clean");
        $this->form_validation->set_rules("kode_proyek", "kode_proyek", "required|xss_clean");

        if ($this->form_validation->run() == TRUE) {
            $field["kode_rekanan"] = $this->input->post("kode_rekanan");
            $field["nama_rekanan"] = $this->input->post("nama_rekanan");
            //$field["nama_kontak"] = $this->input->post("nama_kontak");
            //$field["alamat"] = $this->input->post("alamat");
            //$field["kota"] = $this->input->post("kota");
            //$field["telp_rekanan"] = $this->input->post("telp_rekanan");
            //$field["telp_kontak"] = $this->input->post("telp_kontak");
            //$field["type_rekanan"] = (int) $this->input->post("type_rekanan");
            $field["id_proyek"] = (int) $this->input->post("kode_proyek");
            //$field["create_id"] = (int) $this->session->userdata('ba_user_id');
            //$field["create_time"] = $this->myauth->timestampIndo();

            $insert = $this->rekanan_model->insert($field);
            if ($insert) {
                $data['success'] = "<p>Data Berhasil Di Input</p>";
                $json['json'] = $data;
                $this->load->view('template/ajax', $json);
            } else {
                $data['error'] = "<p>Data Gagal Di Input</p>";
                $json['json'] = $data;
                $this->load->view('template/ajax', $json);
            }
        } else {
            $data['error'] = validation_errors();
            $json['json'] = $data;
            $this->load->view('template/ajax', $json);
        }
    }

    public function autocomplete_rekanan() {
        $param = !empty($_GET['term']) ? $_GET['term'] : '';
        $coa = !empty($_GET['coa']) ? $_GET['coa'] : '';
        $userconfig = $this->dataset_db->getUserconfig($this->session->userdata('ba_user_id'));
        $query = $this->rekanan_model->autocomplete($param, $userconfig["kolom2"], $coa);
        echo json_encode($query);
    }

    public function from_excel() {
        $configuser = $this->rekanan_model->getConfigUser($this->session->userdata('ba_user_id'));
        $domain = $this->rekanan_model->getDomain($configuser["kolom2"]);
        $data["kode_perkiraan"] = json_encode($this->site_library->getKodePerkiraan());
        if ($this->input->post()) {
            $config['file_name'] = 'form_upload_master_rekanan';
            $config['upload_path'] = './files/';
            $config['allowed_types'] = 'xl|xls|xlsx';
            $config['overwrite'] = TRUE;
            $this->load->library('upload', $config);
            if (!$this->upload->do_upload('master_rekanan')) {
                $this->session->set_flashdata('messages', 'File yang anda upload bukan file excel ...');
                redirect('mod_rekanan/from_excel');
                //echo "<script>alert('Data gagal disimpan!". $this->upload->display_errors()."');window.location = '".base_url()."/mod_rekanan/upload_form'</script>";
            } else {
                $files = array('upload_data' => $this->upload->data());
                $file = $files['upload_data']['full_path'];
            }
            //print_r($this->upload->data());
            //echo $file;
            $this->load->library('excel');
            $inputFileType = 'Excel2007';
            $objReader = PHPExcel_IOFactory::createReader($inputFileType);
            $objReader->setReadDataOnly(true);
            $objPHPExcel = $objReader->load($file);
            $objPHPExcel->setActiveSheetIndex(0);
            $objWorksheet = $objPHPExcel->getActiveSheet();
            $highestRow = $objWorksheet->getHighestRow();
            $array_kode = array();
            $temp_result = array();
            $log_status = '';
            $log_error = '';
            if (file_exists($file)) {
                for ($i = 2; $i <= $highestRow; $i++) {
                    if ($this->rekanan_model->checkKodeRekanan($objPHPExcel->getActiveSheet()->getCell('A' . $i)->getValue(), $domain['id_proyek'])) {
                        $log_status .= 'Error';
                        $log_error .= 'Kode rekanan sudah ada';
                    }
                    if ($this->rekanan_model->getIdLibrary(strtoupper($objPHPExcel->getActiveSheet()->getCell('H' . $i)->getValue())) == false) {
                        $log_status .= 'Error';
                        $log_error .= 'Tipe rekanan tidak ada dalam database';
                    }
                    if (in_array($objPHPExcel->getActiveSheet()->getCell('A' . $i)->getValue(), $array_kode)) {
                        $log_status .= 'Error';
                        $log_error .= 'Kode rekanan sudah ada';
                    } else {
                        $array_kode[] = $objPHPExcel->getActiveSheet()->getCell('A' . $i)->getValue();
                    }
                    $temp_result[] = array(
                        'id_proyek' => $domain['id_proyek'],
                        'kode_rekanan' => $objPHPExcel->getActiveSheet()->getCell('A' . $i)->getValue(),
                        'nama_rekanan' => $objPHPExcel->getActiveSheet()->getCell('B' . $i)->getValue(),
                        'telp_rekanan' => $objPHPExcel->getActiveSheet()->getCell('C' . $i)->getValue(),
                        'nama_kontak' => $objPHPExcel->getActiveSheet()->getCell('D' . $i)->getValue(),
                        'alamat' => $objPHPExcel->getActiveSheet()->getCell('E' . $i)->getValue(),
                        'telp_kontak' => $objPHPExcel->getActiveSheet()->getCell('F' . $i)->getValue(),
                        'kota' => $objPHPExcel->getActiveSheet()->getCell('G' . $i)->getValue(),
                        'type_rekanan' => $this->rekanan_model->getIdLibrary(strtoupper($objPHPExcel->getActiveSheet()->getCell('H' . $i)->getValue())),
                        'status' => ($log_status == '' ? 'OK' : $log_status),
                        'keterangan' => ($log_error == '' ? '' : $log_error)
                    );
                    $log_status = '';
                    $log_error = '';
                }
            }
            $data['tmp'] = $temp_result;
            $data['mydata'] = json_encode($temp_result);
        } else {
            $data['tmp'] = false;
            $data['mydata'] = '[]';
        }

        $this->toolbar->create_toolbar();
        $this->toolbar->cGroupButton();
        $this->toolbar->addLink("", "btn tooltips", base_url() . "mod_rekanan", "form_rekanan_list", "cus-application", "List Data User", "tooltip", "right");
        $this->toolbar->addLink("", "btn tooltips", base_url() . "mod_rekanan/form_rekanan_add", "form_rekanan_new", "cus-application-form-add", "Tambah User", "tooltip", "right");
        $this->toolbar->addLink("", "btn tooltips", "#", "form_rekanan_delete", "cus-application-form-delete", "Delete User", "tooltip", "right");
        $this->toolbar->eGroupButton();
//        $this->toolbar->cGroupButton();
//        $this->toolbar->addLink("", "btn tooltips", base_url() . "mod_rekanan/from_excel", "form_rekanan_import_xls", "cus-page-white-excel", "Import Data Excel", "tooltip", "right");
//        $this->toolbar->addLink("", "btn tooltips", base_url() . "mod_rekanan/to_excel", "form_rekanan_export_xls", "cus-page-excel", "Eksport Data Excel", "tooltip", "right");
//        $this->toolbar->eGroupButton();
        $data['toolbars'] = $this->toolbar->generate();
        
        $data['ptitle'] = "Master Rekanan";
        $data['navs'] = $this->dataset_db->buildNav(0);
        $tabs = $this->session->userdata('tabs');
        if (!$tabs)
            $tabs = array();
        $tabs['mod_rekanan'] = $this->dataset_db->getModule('mod_rekanan');
        $this->session->set_userdata('tabs', $tabs);
        $data['unitkerja'] = $this->dataset_db->getSubUnitkerja();
        $data['kode_proyek'] = $this->dataset_db->getDataProyek();

//            $data['kode_proyek'] = $this->rekanan_model->getKodeProyek();
        $data['type_rekanan'] = $this->rekanan_model->getTypeRekanan();
        $data['current_tab'] = $tabs['mod_rekanan']['link'];
        $data['content'] = $this->load->view('rekanan_upload', $data, true);
        $this->load->vars($data);
        $this->load->view('default_view');
    }

    public function send_import() {
        $userconfig = $this->dataset_db->getUserconfig($this->session->userdata('ba_user_id'));
        if ($this->input->post('jqGridData')) {
            $fixData = array();
            $jqGridData = json_decode($this->input->post('jqGridData'), true);
            foreach ($jqGridData as $k => $v) {
                if (array_search('Error', $v) === false) {
                    unset($v['status']);
                    unset($v['keterangan']);
                    $fixData[] = $v;
                }
            }
            //die(print_r($fixData));
            if (count($fixData) > 0) {
                foreach ($fixData as $key => $val) {
                    $kode_perkiraan = explode(", ", $val['kdperkiraan']);
                    //die(print_r($kode_perkiraan));
                    unset($val['kdperkiraan']);
                    unset($val['act']);
                    $insert = $this->rekanan_model->insert($val);
                    foreach ($kode_perkiraan as $value) {
                        $temp_result[] = array(
                            'bukubantu_idproyek' => $userconfig["kolom2"],
                            'bukubantu_kdperkiraan' => $value,
                            'bukubantu_kdrekanan' => $insert,
                            'bukubantu_isrekanan' => 't',
                            'bukubantu_issbdaya' => 'f'
                        );
                    }
                    if ($insert) {
                        $this->rekanan_model->insert_bukubantu($temp_result);
                    }
                }
            }
        }
    }

}
