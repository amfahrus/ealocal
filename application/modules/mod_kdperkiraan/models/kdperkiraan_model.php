<?php

class kdperkiraan_model extends CI_Model {

    protected $_table;
    protected $_countAll;

    public function __construct() {
        parent::__construct();
        $this->config->load('database_tables', TRUE);
        $this->_table = $this->config->item('database_tables');
    }

    public function getAllAkun($limit, $offset, $sidx, $sord, $cari, $search = "false", $id_proyek) {
		$jenis = $this->getJenisProyek($id_proyek);
        if (!is_array($cari))
            $cari = array();

        $this->db->start_cache();
        if ($search == "true") {
            foreach ($cari as $row) {
                if (!empty($row['cols']) AND !empty($row['ops'])) {
                    $fields = explode(":", $row['cols']);
                    switch ($row['ops']) {
                        case "=":
                            $this->db->where($fields[1], strtolower($row['vals']));
                            break;
                        case "!=":
                            $this->db->where($fields[1] . " !=", strtolower($row['vals']));
                            break;
                        case "like":
                            $this->db->like($fields[1], strtolower($row['vals']), 'both');
                            break;
                        case "not like":
                            $this->db->not_like($fields[1], strtolower($row['vals']));
                            break;
                        case ">":
                            $this->db->where($fields[1] . " >", strtolower($row['vals']));
                            break;
                        case "<":
                            $this->db->where($fields[1] . " <", strtolower($row['vals']));
                            break;
                        case ">=":
                            $this->db->where($fields[1] . " >=", strtolower($row['vals']));
                            break;
                        case "<=":
                            $this->db->where($fields[1] . " <=", strtolower($row['vals']));
                            break;
                    }
                }
            }
        }
        $this->db->from($this->_table['tbl_dperkir']);
        $this->db->select('kdperkiraan, nmperkiraan, flag_sumberdaya, flag_nasabah, flag_unit as proyek');
        //$this->db->where('dperkir_jenis_id', $jenis);
        //$this->db->where('dperkir_jenis_id', $jenis);
        $this->_countAll = $this->db->count_all_results();
        $this->db->order_by($sidx, $sord);
        $this->db->limit($limit, $offset);
        $query = $this->db->get($this->_table['tbl_dperkir']);
        $this->db->flush_cache();

        $this->dataset_db->insert_logs(
										array(
											'log_username' => $this->session->userdata('ba_username'),
											'log_node' => $_SERVER['REMOTE_ADDR'],
											'log_description' => 'User menampilkan semua data kode perkiraan di modul kode perkiraan',
											'log_params' => json_encode($cari)
										)
									  );

        $temp_result = array();
        foreach ($query->result_array() as $row) {
            $temp_result[] = array(
                'kdperkiraan' => $row['kdperkiraan'],
                'nmperkiraan' => $row['nmperkiraan'],
                'flag_nasabah' => ($row['flag_nasabah'] == "t") ? '<i class="cus-tick"></i>' : "",
                'flag_sumberdaya' => ($row['flag_sumberdaya'] == "t") ? '<i class="cus-tick"></i>' : "",
                'proyek' => ($row['proyek'] == "t") ? '<i class="cus-tick"></i>' : ""
            );
        }
        return $temp_result;
    }

    public function getAll($limit, $offset, $sidx, $sord, $cari, $search, $id_proyek, $cat) {
		$jenis = $this->getJenisProyek($id_proyek);
        if (!is_array($cari))
            $cari = array();

        if (!empty($cat)) {
            if (strtolower($cat) == "k") {
                $dperkir_id = $this->getDperkirByGroup(array('Kas'));
            } else {
                $dperkir_id = $this->getDperkirByGroup(array('Bank Giro Berelasi','Bank Giro Pihak Ketiga'));
            }
        }

        $this->db->start_cache();
        if ($search == "true") {
            foreach ($cari as $key => $value) {
                if (!empty($value)) {
                    $this->db->like("lower(" . $key . ")", strtolower($value), 'both');
                }
            }
        }

        if (!empty($cat)) {
            if (strtolower($cat) == "k") {
                $this->db->where_in("dperkir_id",$dperkir_id);
            } else {
                $this->db->where_in("dperkir_id",$dperkir_id);
            }
        }

        $this->db->from($this->_table['tbl_dperkir']);
        $this->_countAll = $this->db->count_all_results();
        $this->db->select('dperkir_id,kdperkiraan,nmperkiraan,flag_sumberdaya,flag_nasabah,flag_unit as proyek');
        //$this->db->where('dperkir_jenis_id', $jenis);
        $this->db->order_by($sidx, $sord);
        $this->db->limit($limit, $offset);
        $query = $this->db->get($this->_table['tbl_dperkir']);
        $this->db->flush_cache();

        $temp_result = array();
        foreach ($query->result_array() as $row) {
            $temp_result[] = array(
                'dperkir_id' => $row['dperkir_id'],
                'kdperkiraan' => $row['kdperkiraan'],
                'nmperkiraan' => $row['nmperkiraan'],
                'flag_sumberdaya' => $row['flag_sumberdaya'],
                'flag_nasabah' => $row['flag_nasabah'],
                'proyek' => $row['proyek']
            );
        }
        return $temp_result;
    }

    public function getAllExcel($id_proyek) {
		$jenis = $this->getJenisProyek($id_proyek);
        $this->db->start_cache();
        $this->db->select('dperkir_id,kdperkiraan,nmperkiraan,flag_sumberdaya,flag_nasabah,flag_unit as proyek');
        //$this->db->where('dperkir_jenis_id', $jenis);
        $this->db->order_by('kdperkiraan','asc');
        $query = $this->db->get($this->_table['tbl_dperkir']);
        $this->db->flush_cache();

        $temp_result = array();
        foreach ($query->result_array() as $row) {
			$flag_sumberdaya = $row['flag_sumberdaya'] == 't' ? 'X' : '';
			$flag_nasabah = $row['flag_nasabah'] == 't' ? 'X' : '';
			$flag_proyek = $row['proyek'] == 't' ? 'X' : '';
            $temp_result[] = array(
                'kdperkiraan' => $row['kdperkiraan'],
                'nmperkiraan' => $row['nmperkiraan'],
                'flag_nasabah' => $flag_nasabah,
                'flag_sumberdaya' => $flag_sumberdaya,
                'proyek' => $flag_proyek
            );
        }
        return $temp_result;
    }

    public function countAll() {
        return $this->_countAll;
    }

    public function getpicker($cond,$id_proyek) {
		$jenis = $this->getJenisProyek($id_proyek);
        $this->db->select('dperkir_id,kdperkiraan,nmperkiraan,flag_sumberdaya,flag_nasabah,flag_unit as proyek');
        $this->db->from($this->_table['tbl_dperkir']);
        $this->db->where($cond);
        //$this->db->where("dperkir_jenis_id",$jenis);
        $query = $this->db->get();
        $this->dataset_db->insert_logs(
										array(
											'log_username' => $this->session->userdata('ba_username'),
											'log_node' => $_SERVER['REMOTE_ADDR'],
											'log_description' => 'User memilih data kode perkiraan di tampilan popup',
											'log_params' => json_encode($query->row_array())
										)
									  );

        return $query->row_array();
    }

    public function getPerkiraan($param, $cat = "", $search = false, $id_proyek) {
		$jenis = $this->getJenisProyek($id_proyek);

        if ($search == "true") {
            if (strtolower($cat) == "k") {
                $dperkir_id = $this->getDperkirByGroup(array('Kas'));
                $this->db->where_in("dperkir_id",$dperkir_id);
            } else {
                $dperkir_id = $this->getDperkirByGroup(array('Bank Giro Berelasi','Bank Giro Pihak Ketiga'));
                $this->db->where_in("dperkir_id",$dperkir_id);
            }
        }

        $this->db->start_cache();
        $this->db->select('dperkir_id,kdperkiraan,nmperkiraan,flag_sumberdaya,flag_nasabah,flag_unit as proyek');
        //$this->db->where("dperkir_jenis_id",$jenis);
        $this->db->order_by("kdperkiraan", "asc");
        $this->db->like("lower(kdperkiraan)", strtolower($param), 'both');
        $query = $this->db->get($this->_table['tbl_dperkir']);
        $this->db->flush_cache();

        $temp_result = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {
                $temp_result[] = array(
                    'id' => $row['dperkir_id'],
                    'kode' => $row['kdperkiraan'],
                    'desc' => $row['nmperkiraan'],
                    'label' => $row['kdperkiraan'] . " - " . $row['nmperkiraan'],
                    "flag_sumberdaya" => $row["flag_sumberdaya"],
                    "flag_nasabah" => $row["flag_nasabah"],
                    "proyek" => $row["proyek"]
                );
            }
        } else {
            $temp_result[] = array(
                'id' => 0,
                'kode' => "",
                'desc' => "Not Found",
                'label' => "Record Not Found",
                "flag_sumberdaya" => "",
                "flag_nasabah" => "",
                "proyek" => ""
            );
        }
        return $temp_result;
    }

	private function getJenisProyek($id_proyek){
		$this->db->select("dperkir_jenis_id");
		$this->db->from("tbl_proyek");
		$this->db->where("id_proyek",$id_proyek);
		$query = $this->db->get();
		$row = $query->row_array();
		return $row['dperkir_jenis_id'];
	}

  private function getDperkirByGroup($gname){
    //$this->db->start_cache();
    $temp_result = array();
		$this->db->select("a.dperkir_id");
		$this->db->from("tbl_dperkir a");
    $this->db->join("tbl_dperkir_group_relasi b","a.dperkir_id = b.dperkir_id","left");
    $this->db->join("tbl_dperkir_group c","b.dperkir_group_id = c.dperkir_group_id","left");
		$this->db->where_in("c.gname",$gname);
		$query = $this->db->get();
    if ($query->num_rows() > 0) {
        foreach ($query->result_array() as $row) {
            $temp_result[] = $row['dperkir_id'];
        }
    }
    //$this->db->flush_cache();
		return $temp_result;
	}

}
