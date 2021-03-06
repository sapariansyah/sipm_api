<?php

namespace App;

use Thybag\SharePointAPI;
use App\Helpers;

class Dao {

    private $logger;
    private $sp_client;

    // Constructor
    public function __construct($container) {
        $this->sp_client = new SharePointAPI(
            $container->get('settings')['spuser']['username'], 
            $container->get('settings')['spuser']['password'], 
            $container->get('settings')['spuser']['wsdl_path'], 
            'NTLM'
        );
        $this->sp_client->lowercaseIndexs(FALSE);
    }

    public function getTemaPengawasan($request) {
        $select = [
            'ID' => 'id_tema_pengawasan',
            'TemaPengawasan' => 'tema_pengawasan'
        ];

        $list_tema_pengawasan = $this->sp_client
                ->query('MasterTemaPengawasan')
                ->fields(array_keys($select));
        
        $list_tema_pengawasan = Helpers::createResults($list_tema_pengawasan->get(), $select, ['id_tema_pengawasan' => DATA_TYPE_INTEGER]);

        return $list_tema_pengawasan;
    }

    public function getJenisPemeriksaan($request) {
        $select = [
            'ID' => 'id_jenis_pemeriksaan',
            'JenisPemeriksaan' => 'jenis_pemeriksaan'
        ];

        $list_jenis_pemeriksaan = $this->sp_client
                ->query('MasterJenisPemeriksaan')
                ->fields(array_keys($select));
        
        $list_jenis_pemeriksaan = Helpers::createResults($list_jenis_pemeriksaan->get(), $select, ['id_jenis_pemeriksaan' => DATA_TYPE_INTEGER]);

        return $list_jenis_pemeriksaan;
    }

    public function getMonitoringTanggapan($request) {

        // Filter Perusahaan
        $select = [
            'ID' => 'id_profil',
            'NamaPihak' => 'perusahaan',
        ];

        $list_perusahaan = $this->sp_client
            ->query('MasterProfil')
            ->fields(array_keys($select));
        $list_perusahaan = $list_perusahaan->where("JenisPihak", "=", "Perusahaan");

        if ($request->getQueryParam('perusahaan')) {
            $list_perusahaan = $list_perusahaan->and_where('NamaPihak','contains', $request->getQueryParam('perusahaan'));
        }

        $list_perusahaan = Helpers::createLOV($list_perusahaan->get(), $select);

        if ($request->getQueryParam('perusahaan') && !count($list_perusahaan)) return [];

        // Filter surat tugas
        $select = [
            'ID' => 'id_surat_tugas',
            'NomorSuratTugas' => 'nomor_surat_tugas',
            'AwalPeriode' => 'awal_periode',
            'AkhirPeriode' => 'akhir_periode',
            'TemaPengawasan' => 'id_tema_pengawasan',
            'JenisPemeriksaan' => 'id_jenis_pemeriksaan',
            'Lokasi' => 'lokasi',
        ];

        $list_surat_tugas = $this->sp_client
            ->query('MasterSuratTugas')
            ->fields(array_keys($select));
        $list_surat_tugas = $list_surat_tugas
            ->where('Direktorat','=', 'DPLE');

        if ($request->getQueryParam('awal_periode')) 
            $list_surat_tugas = $list_surat_tugas->and_where('AwalPeriode','>=',\Thybag\SharepointApi::dateTime($request->getQueryParam('awal_periode')));
        if ($request->getQueryParam('akhir_periode')) 
            $list_surat_tugas = $list_surat_tugas->and_where('AkhirPeriode','<=',\Thybag\SharepointApi::dateTime($request->getQueryParam('akhir_periode')));
        if ($request->getQueryParam('nomor_surat_tugas')) 
            $list_surat_tugas = $list_surat_tugas->and_where('NomorSuratTugas','contains', $request->getQueryParam('nomor_surat_tugas'));
        if ($request->getQueryParam('lokasi')) 
            $list_surat_tugas = $list_surat_tugas->and_where('Lokasi','=', $request->getQueryParam('lokasi'));
        
        $list_surat_tugas = Helpers::createLOV($list_surat_tugas->get(), $select);
        
        if (($request->getQueryParam('awal_periode') || $request->getQueryParam('nomor_surat_tugas') || $request->getQueryParam('akhir_periode')) && !count($list_surat_tugas)) return [];

        // Filter UserAccount
        $select = [
            'ID' => 'id_user_account',
            'NamaLengkap' => 'nama_lengkap',
        ];

        $list_user_account = $this->sp_client
            ->query('UserAccount')
            ->fields(array_keys($select));

        if ($request->getQueryParam('pic')) 
            $list_user_account = $list_user_account->where('NamaLengkap','contains', $request->getQueryParam('pic'));

        $list_user_account = Helpers::createLOV($list_user_account->get(), $select);
        
        if ($request->getQueryParam('pic') && !count($list_user_account)) return [];
        
        // LOV jenis pemeriksaan
        $select = [
            'ID' => 'id_jenis_pemeriksaan',
            'JenisPemeriksaan' => 'jenis_pemeriksaan'
        ];

        $list_jenis_pemeriksaan = $this->sp_client
            ->query('MasterJenisPemeriksaan')
            ->fields(array_keys($select));
        
        if ($request->getQueryParam('jenis_pemeriksaan')){
            $list_jenis_pemeriksaan = $list_jenis_pemeriksaan->where('ID', '=', $request->getQueryParam('jenis_pemeriksaan'));
        }

        $list_jenis_pemeriksaan = Helpers::createLOV($list_jenis_pemeriksaan->get(), $select);

        if ($request->getQueryParam('jenis_pemeriksaan') && !count($list_jenis_pemeriksaan)) return [];

        // LOV tema pengawasan
        $select = [
            'ID' => 'id_tema_pengawasan',
            'TemaPengawasan' => 'tema_pengawasan'
        ];

        $list_tema_pengawasan = $this->sp_client
                ->query('MasterTemaPengawasan')
                ->fields(array_keys($select));
        
        if ($request->getQueryParam('tema_pengawasan')){
            $list_tema_pengawasan = $list_tema_pengawasan->where('ID', '=', $request->getQueryParam('tema_pengawasan'));
        }

        $list_tema_pengawasan = Helpers::createLOV($list_tema_pengawasan->get(), $select);

        if ($request->getQueryParam('tema_pengawasan') && !count($list_tema_pengawasan)) return [];

        // Filter SHP Pihak
        $select = [
            'ID' => 'id_shp_pihak',
            'MasterProfil' => 'id_profil',
            'DPLEKesimpulanPihak' => 'id_kesimpulan_pihak',
        ];

        $list_shp_pihak = $this->sp_client
            ->query('DPLESHPPihak')
            ->fields(array_keys($select));
        $list_shp_pihak = $list_shp_pihak->where('DPLEKesimpulanPihak','not_null', '');

        $list_shp_pihak = Helpers::createLOV($list_shp_pihak->get(), $select);

        if ($request->getQueryParam('nomor_surat_ojk') && !count($list_shp_pihak)) return [];

        // Filter SHP
        $select = [
            'ID' => 'id_shp',
            'MasterSuratTugas' => 'id_surat_tugas',
            'NomorSurat' => 'nomor_surat_ojk',
        ];

        $list_shp = $this->sp_client
            ->query('DPLEshp')
            ->fields(array_keys($select));
        $list_shp = $list_shp->where('MasterSuratTugas','not_null', '');

        if ($request->getQueryParam('nomor_surat_ojk')) {
            $list_shp = $list_shp->and_where('NomorSurat','contains',$request->getQueryParam('nomor_surat_ojk'));
        }

        $list_shp = Helpers::createLOV($list_shp->get(), $select);

        if ($request->getQueryParam('nomor_surat_ojk') && !count($list_shp)) return [];

        // Filter PIC
        $select = [
            'ID' => 'id_pic',
            'SuratTugas' => 'id_surat_tugas',
            'UserAccount' => 'id_user_account',
        ];

        $list_tim_surat = $this->sp_client
            ->query('TimSuratTugas')
            ->fields(array_keys($select));
        $list_tim_surat = $list_tim_surat->where('PIC','=', 1)
            ->and_where('UserAccount', 'not_null', '')
            ->and_where('SuratTugas', 'not_null', '');

        $list_tim_surat = Helpers::createLOV($list_tim_surat->get(), $select, "SuratTugas");

        // Filter Temuan
        $select = [
            'ID' => 'id_kesimpulan_pihak',
            'DPLEshp' => 'id_shp',
            'Temuan' => 'temuan',
            "SisaWaktu" => 'sisa_waktu',
        ];

        $list_kesimpulan_pihak = $this->sp_client
            ->query('DPLEKesimpulanPihak')
            ->fields(array_keys($select));

        $list_kesimpulan_pihak = $list_kesimpulan_pihak
            ->where('DPLEshp','not_null', '');

        if ($request->getQueryParam('temuan')) 
            $list_kesimpulan_pihak = $list_kesimpulan_pihak->and_where('Temuan','contains', $request->getQueryParam('temuan'));

        $list_kesimpulan_pihak = Helpers::createLOV($list_kesimpulan_pihak->get(), $select, 'ID', ['sisa_waktu' => DATA_TYPE_INTEGER]);
        // var_dump($list_kesimpulan_pihak);
        if ($request->getQueryParam('temuan') && count($list_kesimpulan_pihak)) return [];

        // return $list_kesimpulan_pihak;
        // Create structure data
        $data = [];
        foreach ($list_shp_pihak as $id_shp_pihak => $shp_pihak) {
            $perusahaan = isset($list_perusahaan[$shp_pihak['id_profil']])? $list_perusahaan[$shp_pihak['id_profil']]['perusahaan']: "";
            
            if (!$perusahaan) continue;

            $lookup_kesimpulan_pihak = isset($list_kesimpulan_pihak[$shp_pihak['id_kesimpulan_pihak']])? $list_kesimpulan_pihak[$shp_pihak['id_kesimpulan_pihak']]: false;
            if (!$lookup_kesimpulan_pihak) continue;

            
            // Filter sisa waktu
            $sisa_waktu = (int)$lookup_kesimpulan_pihak['sisa_waktu'];

            if ($sisa_waktu < 0) continue;

            if ($request->getQueryParam('sisa_waktu_kurang')) {
                if ($sisa_waktu >= (int)$request->getQueryParam('sisa_waktu_kurang')) continue;
            } else if ($request->getQueryParam('sisa_waktu_lebih')) {
                if ($sisa_waktu <= (int)$request->getQueryParam('sisa_waktu_lebih')) continue;
            } else if ($request->getQueryParam('sisa_waktu')) {
                if ($sisa_waktu < (int)$request->getQueryParam('sisa_waktu') || $sisa_waktu > (int)$request->getQueryParam('sisa_waktu')) continue;
            }

            $lookup_shp = isset($list_shp[$lookup_kesimpulan_pihak['id_shp']])? $list_shp[$lookup_kesimpulan_pihak['id_shp']]: false;

            $lookup_surat_tugas = isset($list_surat_tugas[$lookup_shp['id_surat_tugas']])? $list_surat_tugas[$lookup_shp['id_surat_tugas']] : false;

            if ( !$lookup_surat_tugas && ($request->getQueryParam('awal_periode') 
                || $request->getQueryParam('akhir_periode') 
                || $request->getQueryParam('nomor_surat_tugas')
                || $request->getQueryParam('lokasi')))
                continue;

            $lookup_tim_surat = isset($list_tim_surat[$lookup_surat_tugas['id_surat_tugas']])? $list_tim_surat[$lookup_surat_tugas['id_surat_tugas']]: false;

            $pic = "";
            if ($lookup_tim_surat) {
                $pic = isset($list_user_account[$lookup_tim_surat['id_user_account']])? $list_user_account[$lookup_tim_surat['id_user_account']]['nama_lengkap']: '';
            }

            if (!$pic && $request->getQueryParam('pic')) continue;

            $jenis_pemeriksaan = isset($list_jenis_pemeriksaan[$lookup_surat_tugas['id_jenis_pemeriksaan']])? $list_jenis_pemeriksaan[$lookup_surat_tugas['id_jenis_pemeriksaan']]['jenis_pemeriksaan']: "";
            if (!$jenis_pemeriksaan) return [];
            
            $tema_pengawasan = isset($list_tema_pengawasan[$lookup_surat_tugas['id_tema_pengawasan']])? $list_tema_pengawasan[$lookup_surat_tugas['id_tema_pengawasan']]['tema_pengawasan']: "";
            if (!$tema_pengawasan) return [];

            $data[] = [
                'id_shp_pihak' => $shp_pihak['id_shp_pihak'],
                'id_surat_tugas' => $lookup_surat_tugas['id_surat_tugas'],
                'perusahaan' => $perusahaan,
                'nomor_surat_ojk' => $lookup_shp['nomor_surat_ojk'],
                'id_surat_tugas' => $lookup_surat_tugas['id_surat_tugas'],
                'nomor_surat_tugas' => $lookup_surat_tugas['nomor_surat_tugas'],
                'temuan' => $lookup_kesimpulan_pihak['temuan'],
                'awal_periode' => $lookup_surat_tugas['awal_periode'],
                'akhir_periode' => $lookup_surat_tugas['akhir_periode'],
                'pic' => $pic,
                'jenis_pemeriksaan'=> $jenis_pemeriksaan,
                'tema_pengawasan' => $tema_pengawasan,
                'lokasi' => $lookup_surat_tugas['lokasi'],
                'sisa_waktu' => $lookup_kesimpulan_pihak['sisa_waktu']
            ];
        }
        
        return $data;
    }

    public function getPelanggaranPerPerusahaan($request) {

        // Filter Perusahaan
        $select = [
            'ID' => 'id_profil',
            'NamaPihak' => 'perusahaan',
        ];

        $list_perusahaan = $this->sp_client
            ->query('MasterProfil')
            ->fields(array_keys($select));
        $list_perusahaan = $list_perusahaan->where("JenisPihak", "=", "Perusahaan");

        if ($request->getQueryParam('perusahaan')) {
            $list_perusahaan = $list_perusahaan->and_where('NamaPihak','contains', $request->getQueryParam('perusahaan'));
        }

        $list_perusahaan = Helpers::createResults($list_perusahaan->get(), $select);

        if ($request->getQueryParam('perusahaan') && !count($list_perusahaan)) return [];

        return $list_perusahaan;

        // Filter Profil Perusahaan
        $select = [
            'ID' => 'id_profil_perusahaan',
            'MasterProfil' => 'id_profil',
            'KodePihak' => 'kode_perusahaan',
        ];

        $list_profil_perusahaan = $this->sp_client
            ->query('ProfilPihakInstitusi')
            ->fields(array_keys($select))
            ->where("MasterProfil", "not_null", "");

        // Filter kode perusahaan
        if ($request->getQueryParam('kode_perusahaan')) {
            $list_profil_perusahaan = $list_profil_perusahaan->and_where('KodePihak','contains', $request->getQueryParam('kode_perusahaan'));
        }

        $list_profil_perusahaan = Helpers::createResults($list_profil_perusahaan->get(), $select);

        if ($request->getQueryParam('perusahaan') && !count($list_profil_perusahaan)) return [];

        // Filter surat tugas
        $select = [
            'ID' => 'id_surat_tugas',
            'AwalPeriode' => 'awal_periode',
            'AkhirPeriode' => 'akhir_periode',
        ];

        $list_surat_tugas = $this->sp_client
            ->query('MasterSuratTugas')
            ->fields(array_keys($select));
        $list_surat_tugas = $list_surat_tugas
            ->where('Direktorat','=', 'DPLE');

        if ($request->getQueryParam('awal_periode')) 
            $list_surat_tugas = $list_surat_tugas->and_where('AwalPeriode','>=',\Thybag\SharepointApi::dateTime($request->getQueryParam('awal_periode')));
        if ($request->getQueryParam('akhir_periode')) 
            $list_surat_tugas = $list_surat_tugas->and_where('AkhirPeriode','<=',\Thybag\SharepointApi::dateTime($request->getQueryParam('akhir_periode')));
        
        $list_surat_tugas = Helpers::createLOV($list_surat_tugas->get(), $select);
        
        if (($request->getQueryParam('awal_periode') || $request->getQueryParam('akhir_periode')) 
            && !count($list_surat_tugas)) return [];

        // Filter SHP Pihak
        $select = [
            'ID' => 'id_shp_pihak',
            'MasterProfil' => 'id_profil',
            'DPLEKesimpulanPihak' => 'id_kesimpulan_pihak',
        ];

        $list_shp_pihak = $this->sp_client
            ->query('DPLESHPPihak')
            ->fields(array_keys($select));
        $list_shp_pihak = $list_shp_pihak->where('DPLEKesimpulanPihak','not_null', '');

        $list_shp_pihak = Helpers::createLOV($list_shp_pihak->get(), $select);

        if (!count($list_shp_pihak)) return [];

        // Filter SHP
        $select = [
            'ID' => 'id_shp',
            'MasterSuratTugas' => 'id_surat_tugas',
        ];

        $list_shp = $this->sp_client
            ->query('DPLEshp')
            ->fields(array_keys($select));
        $list_shp = $list_shp->where('MasterSuratTugas','not_null', '');

        $list_shp = Helpers::createLOV($list_shp->get(), $select);

        if (!count($list_shp)) return [];


        // Filter SHP Peraturan
        $select = [
            'ID' => 'id_shp_peraturan',
            'DPLEKesimpulanPihak' => 'id_kesimpulan_pihak',
            'Peraturan' => 'id_peraturan',
        ];

        $list_shp_peraturan = $this->sp_client
            ->query('DPLEshpPeraturan')
            ->fields(array_keys($select));
        $list_shp_peraturan = $list_shp_peraturan
            ->where('DPLEKesimpulanPihak','not_null', '')
            ->and_where('Peraturan','not_null', '');

        $list_shp_peraturan = Helpers::createLOV($list_shp_peraturan->get(), $select);

        if (!count($list_shp_peraturan)) return [];

        // Filter Peraturan
        $select = [
            'ID' => 'id_peraturan',
            'Peraturan' => 'peraturan',
            'Level' => 'level',
            'Parent' => 'id_parent',
        ];

        $list_peraturan = $this->sp_client
            ->query('MasterPeraturan')
            ->fields(array_keys($select))
            ->where('Peraturan', 'not_null', '');

        $list_peraturan = Helpers::createLOV($list_peraturan->get(), $select);

        if (!count($list_peraturan)) return [];

        $table_peraturan = $list_peraturan;

        foreach ($list_peraturan as $id_peraturan => $peraturan) {
            $level1 = Helpers::getParentByLevel(1, $id_peraturan, $table_peraturan);
            $level2 = Helpers::getParentByLevel(2, $id_peraturan, $table_peraturan);
            $level3 = Helpers::getParentByLevel(3, $id_peraturan, $table_peraturan);
            
            // if (!($level1 && $level2 && $level3)) {
            //     // unset($list_peraturan[$id_peraturan]);
            //     continue;
            // }

            $list_peraturan[$id_peraturan]['level'] = (int) $list_peraturan[$id_peraturan]['level'];
            $list_peraturan[$id_peraturan]['level1'] = $level1;
            $list_peraturan[$id_peraturan]['level2'] = $level2;
            $list_peraturan[$id_peraturan]['level3'] = $level3;

            $list_grup_peraturan = [$list_peraturan[$id_peraturan]['level1'], $list_peraturan[$id_peraturan]['level2'], $list_peraturan[$id_peraturan]['level3']];

            $list_peraturan[$id_peraturan]['grup_peraturan'] = implode(".", array_filter($list_grup_peraturan));
        }

        return $list_peraturan;

        // Filter kesimpulan_pihak
        $select = [
            'ID' => 'id_kesimpulan_pihak',
            'DPLEshp' => 'id_shp',
            'Temuan' => 'temuan',
        ];

        $list_kesimpulan_pihak = $this->sp_client
            ->query('DPLEKesimpulanPihak')
            ->fields(array_keys($select));

        $list_kesimpulan_pihak = $list_kesimpulan_pihak
            ->where('DPLEshp','not_null', '');

        $list_kesimpulan_pihak = Helpers::createLOV($list_kesimpulan_pihak->get(), $select);

        // Join perusahaan into shp_pihak, key = id_shp_pihak
        foreach ($list_shp_pihak as $id_shp_pihak => $shp_pihak) {
            $list_shp_pihak[$id_shp_pihak]['perusahaan'] = isset($list_perusahaan[$shp_pihak['id_profil']])? $list_perusahaan[$shp_pihak['id_profil']]['perusahaan']: null;
        }

        // Join peraturan into shp_peraturan
        foreach ($list_shp_peraturan as $id_shp_peraturan => $shp_peraturan) {
            $list_shp_peraturan[$id_shp_peraturan]['peraturan'] = isset($shp_peraturan['id_peraturan'])? $list_peraturan[$shp_peraturan['id_peraturan']]: [];
        }

        return $list_kesimpulan_pihak;
    }

    public function getPeraturan($request) {

        $select = [
            'ID' => 'id_peraturan',
            'Peraturan' => 'peraturan',
            'Level' => 'level',
            'Parent' => 'id_parent',
            'Keterangan' => 'keterangan',
        ];

        $list_peraturan = $this->sp_client
            ->query('MasterPeraturan')
            ->fields(array_keys($select))
            ->where('Peraturan', 'not_null', '');

        /**
         * START - FILTERING
         */
        if ($request->getQueryParam('peraturan')) {
            $list_peraturan = $list_peraturan->and_where('Peraturan','=', $request->getQueryParam('peraturan'));
        }

        if ($request->getQueryParam('keterangan')) {
            $list_peraturan = $list_peraturan->and_where('Keterangan','contains', $request->getQueryParam('keterangan'));
        }

        if ($request->getQueryParam('level')) {
            if (intval($request->getQueryParam('level')) > 3) return [];
            $list_peraturan = $list_peraturan->and_where('Level','=', $request->getQueryParam('level'));
        }

        /**
         * END - FILTERING
         */

        $list_peraturan = Helpers::createLOV($list_peraturan->get(), $select, "ID",['level' => DATA_TYPE_INTEGER, 'id_peraturan' => DATA_TYPE_INTEGER, 'id_parent' => DATA_TYPE_INTEGER]);
        
        if (!count($list_peraturan)) return [];

        $table_peraturan = $list_peraturan;

        foreach ($list_peraturan as $id_peraturan => $peraturan) {
            $level1 = Helpers::getParentByLevel(1, $id_peraturan, $table_peraturan);
            $level2 = Helpers::getParentByLevel(2, $id_peraturan, $table_peraturan);
            $level3 = Helpers::getParentByLevel(3, $id_peraturan, $table_peraturan);
            
            // if (!($level1 && $level2 && $level3)) {
            //     // unset($list_peraturan[$id_peraturan]);
            //     continue;
            // }

            $list_peraturan[$id_peraturan]['level'] = (int) $list_peraturan[$id_peraturan]['level'];
            $list_peraturan[$id_peraturan]['level1'] = $level1;
            $list_peraturan[$id_peraturan]['level2'] = $level2;
            $list_peraturan[$id_peraturan]['level3'] = $level3;

            $list_tracking_peraturan = Helpers::trackingPeraturan($table_peraturan, $id_peraturan);
            $list_grup_peraturan = [$list_peraturan[$id_peraturan]['level1'], $list_peraturan[$id_peraturan]['level2'], $list_peraturan[$id_peraturan]['level3']];

            $list_peraturan[$id_peraturan]['grup_peraturan'] = implode(".", array_filter($list_grup_peraturan));
            $list_peraturan[$id_peraturan]['tracking_peraturan'] = implode(".", array_reverse($list_tracking_peraturan));
        }

        if ($request->getQueryParam('id_peraturan')) {
            return $list_peraturan[$request->getQueryParam('id_peraturan')];
        }

        return array_values($list_peraturan);
    }

    public function getPihak($request) {
        $select = [
            'ID' => 'id_pihak',
            'NamaPihak' => 'pihak',
            'JenisPihak' => 'jenis_pihak',
            'IzinOJK' => 'izin_ojk',
        ];

        $list_pihak = $this->sp_client
                ->query('MasterProfil')
                ->fields(array_keys($select))
                ->where('JenisPihak', 'not_null', '');

        if ($request->getQueryParam('jenis_pihak')) {
            $list_pihak = $list_pihak->and_where('JenisPihak','=', $request->getQueryParam('jenis_pihak'));
        }
    
        if ($request->getQueryParam('pihak')) {
            $list_pihak = $list_pihak->and_where('NamaPihak', 'contains', $request->getQueryParam('pihak'));
        }

        $list_pihak = Helpers::createResults($list_pihak->get(), $select, ['id_pihak' => DATA_TYPE_INTEGER]);

        if (!count($list_pihak)) return [];

        return $list_pihak;
    }

    public function getPihakInstitusi($request) {
        $select = [
            'ID' => 'id_pihak_institusi',
            'MasterProfil' => 'id_pihak',
            'KodePihak' => 'kode_perusahaan',
            'Npwp' => 'npwp',
            'AlamatKantor' => 'alamat',
        ];

        $list_profil_institusi = $this->sp_client
                ->query('ProfilPihakInstitusi')
                ->fields(array_keys($select))
                ->where('MasterProfil', 'not_null', '');

        if ($request->getQueryParam('kode_perusahaan')) {
            $list_profil_institusi = $list_profil_institusi->and_where('KodePihak','=', $request->getQueryParam('kode_perusahaan'));
        }

        if ($request->getQueryParam('direktorat')) {
            if (strtolower($request->getQueryParam('direktorat')) === 'lain') {
                $list_profil_institusi = $list_profil_institusi->and_where('Direktorat_Lain','=', true);   
            } else if (strtolower($request->getQueryParam('direktorat')) === 'dple' ||
                strtolower($request->getQueryParam('direktorat')) === 'dpiv' ||
                strtolower($request->getQueryParam('direktorat')) === 'dpkr'){
                $list_profil_institusi = $list_profil_institusi->and_where('Direktorat_' . strtoupper($request->getQueryParam('direktorat')),'=', true);    
            }
        }
    
        $list_profil_institusi = Helpers::createResults($list_profil_institusi->get(), $select, ['id_pihak_institusi' => DATA_TYPE_INTEGER, 'id_pihak' => DATA_TYPE_INTEGER]);

        if (!count($list_profil_institusi)) return [];

        return $list_profil_institusi;
    }

    public function getPihakIndividu($request) {
        $select = [
            'ID' => 'id_pihak_individu',
            'MasterProfil' => 'id_pihak',
            'TempatLahir' => 'tempat_lahir',
            'JenisKelamin' => 'jenis_kelamin',
            'TanggalLahir' => 'tanggal_lahir',
        ];

        $list_profil_individu = $this->sp_client
                ->query('MasterProfilPihak')
                ->fields(array_keys($select))
                ->where('MasterProfil', 'not_null', '');

        if ($request->getQueryParam('tempat_lahir')) {
            $list_profil_individu = $list_profil_individu->and_where('TempatLahir','contains', $request->getQueryParam('tempat_lahir'));
        }
    
        $list_profil_individu = Helpers::createResults($list_profil_individu->get(), $select, ['id_pihak_individu' => DATA_TYPE_INTEGER, 'id_pihak' => DATA_TYPE_INTEGER]);

        if (!count($list_profil_individu)) return [];

        return $list_profil_individu;
    }

    public function getIdentitasIndividu($request) {
        $select = [
            'ID' => 'id_identitas_individu',
            'JenisIdentitas' => 'jenis_identitas',
            'NomorIdentitas' => 'nomor_identitas',
            'ProfilPihak' => 'id_pihak_individu',
        ];

        $list_identitas_individu = $this->sp_client
                ->query('IdentitasProfilPihak')
                ->fields(array_keys($select))
                ->where('ProfilPihak', 'not_null', '');

        if ($request->getQueryParam('jenis_identitas')) {
            $list_identitas_individu = $list_identitas_individu->and_where('JenisIdentitas','=', $request->getQueryParam('jenis_identitas'));
        }

        if ($request->getQueryParam('nomor_identitas')) {
            $list_identitas_individu = $list_identitas_individu->and_where('NomorIdentitas','=', $request->getQueryParam('nomor_identitas'));
        }
    
        $list_identitas_individu = Helpers::createResults($list_identitas_individu->get(), $select, ['id_identitas_individu' => DATA_TYPE_INTEGER, 'id_pihak_individu' => DATA_TYPE_INTEGER]);

        if (!count($list_identitas_individu)) return [];

        return $list_identitas_individu;
    }

    public function getAlamatIndividu($request) {
        $select = [
            'ID' => 'id_alamat_individu',
            'JenisAlamat' => 'jenis_alamat',
            'Alamat' => 'alamat',
            'ProfilPihak' => 'id_pihak_individu',   
        ];

        $list_alamat_individu = $this->sp_client
                ->query('AlamatProfilPIhak')
                ->fields(array_keys($select))
                ->where('ProfilPihak', 'not_null', '');

        if ($request->getQueryParam('jenis_alamat')) {
            $list_alamat_individu = $list_alamat_individu->and_where('JenisAlamat','=', $request->getQueryParam('jenis_alamat'));
        }

        if ($request->getQueryParam('alamat')) {
            $list_alamat_individu = $list_alamat_individu->and_where('Alamat','contains', $request->getQueryParam('alamat'));
        }
    
        $list_alamat_individu = Helpers::createResults($list_alamat_individu->get(), $select, ['id_alamat_individu' => DATA_TYPE_INTEGER, 'id_pihak_individu' => DATA_TYPE_INTEGER]);

        if (!count($list_alamat_individu)) return [];

        return $list_alamat_individu;
    }

    public function getShp($request) {
        $select = [
            'ID' => 'id_shp',
            'MasterSuratTugas' => 'id_surat_tugas',
        ];

        $list_shp = $this->sp_client
                ->query('DPLEshp')
                ->fields(array_keys($select))
                ->where('MasterSuratTugas', 'not_null', '');

        /**
         * START - FILTERING
         */
        
        if ($request->getQueryParam('id_shp')) {
            $list_shp = $list_shp->and_where('ID','=', $request->getQueryParam('id_shp'));
        }

        if ($request->getQueryParam('id_surat_tugas')) {
            $list_shp = $list_shp->and_where('MasterSuratTugas','=', $request->getQueryParam('id_surat_tugas'));
        }

        /**
         * END - FILTERING
         */

        $list_shp = Helpers::createResults($list_shp->get(), $select, ['id_shp' => DATA_TYPE_INTEGER, 'id_surat_tugas' => DATA_TYPE_INTEGER]);

        if (!count($list_shp)) return [];

        return $list_shp;
    }

    public function getSuratTugas($request) {
        $select = [
            'ID' => 'id_surat_tugas',
            'NomorSuratTugas' => 'nomor_surat_tugas',
            'AwalPeriode' => 'awal_periode',
            'AkhirPeriode' => 'akhir_periode',
            'TemaPengawasan' => 'id_tema_pengawasan',
            'JenisPemeriksaan' => 'id_jenis_pemeriksaan',
            'Lokasi' => 'lokasi',
        ];

        $list_surat_tugas = $this->sp_client
            ->query('MasterSuratTugas')
            ->fields(array_keys($select));

        /**
         * START - FILTERING
         */
        $list_surat_tugas = $list_surat_tugas
            ->where('Direktorat','=', 'DPLE');

        if ($request->getQueryParam('id_surat_tugas')) 
            $list_surat_tugas = $list_surat_tugas->and_where('ID','=', $request->getQueryParam('id_surat_tugas'));

        if ($request->getQueryParam('awal_periode')) 
            $list_surat_tugas = $list_surat_tugas->and_where('AwalPeriode','>=',\Thybag\SharepointApi::dateTime($request->getQueryParam('awal_periode')));

        if ($request->getQueryParam('akhir_periode')) 
            $list_surat_tugas = $list_surat_tugas->and_where('AkhirPeriode','<=',\Thybag\SharepointApi::dateTime($request->getQueryParam('akhir_periode')));

        if ($request->getQueryParam('nomor_surat_tugas')) 
            $list_surat_tugas = $list_surat_tugas->and_where('NomorSuratTugas','contains', $request->getQueryParam('nomor_surat_tugas'));

        if ($request->getQueryParam('lokasi')) 
            $list_surat_tugas = $list_surat_tugas->and_where('Lokasi','=', $request->getQueryParam('lokasi'));
        /**
         * END - FILTERING
         */
        
        $list_surat_tugas = Helpers::createResults($list_surat_tugas->get(), $select, ['id_surat_tugas' => DATA_TYPE_INTEGER, 'id_tema_pengawasan' => DATA_TYPE_INTEGER, 'id_jenis_pemeriksaan' => DATA_TYPE_INTEGER]);
        
        if (($request->getQueryParam('awal_periode') || $request->getQueryParam('nomor_surat_tugas') || $request->getQueryParam('akhir_periode')) && !count($list_surat_tugas)) return [];

        if (!count($list_surat_tugas)) return [];

        return $list_surat_tugas;
    }

    public function getShpKesimpulanPihak($request) {
        $select = [
            'ID' => 'id_shp_kesimpulan_pihak',
            'DPLEshp' => 'id_shp',
            'Temuan' => 'temuan',
            'JenisRekomendasi' => 'id_jenis_rekomendasi'
        ];

        $list_shp_kesimpulan_pihak = $this->sp_client
                ->query('DPLEKesimpulanPihak')
                ->fields(array_keys($select))
                ->where('DPLEshp', 'not_null', '');
        
        /**
         * START - FILTERING
         */
        
        if ($request->getQueryParam('id_shp_kesimpulan_pihak')) 
            $list_shp_kesimpulan_pihak = $list_shp_kesimpulan_pihak->and_where('ID','=', $request->getQueryParam('id_shp_kesimpulan_pihak'));

        if ($request->getQueryParam('id_shp')) 
            $list_shp_kesimpulan_pihak = $list_shp_kesimpulan_pihak->and_where('DPLEshp','=', \Thybag\SharepointApi::lookup($request->getQueryParam('id_shp'), 'SHP-123'));

        if ($request->getQueryParam('temuan')) 
            $list_shp_kesimpulan_pihak = $list_shp_kesimpulan_pihak->and_where('Temuan','contains', $request->getQueryParam('temuan'));

        /**
         * END - FILTERING
         */

        $list_shp_kesimpulan_pihak = Helpers::createResults($list_shp_kesimpulan_pihak->get(), $select, ['id_shp_kesimpulan_pihak' => DATA_TYPE_INTEGER, 'id_shp' => DATA_TYPE_INTEGER, 'id_jenis_rekomendasi' => DATA_TYPE_INTEGER]);

        if (!count($list_shp_kesimpulan_pihak)) return [];

        return $list_shp_kesimpulan_pihak;
    }

    public function getShpPeraturan($request) {
        $select = [
            'ID' => 'id_shp_peraturan',
            'Peraturan' => 'id_peraturan',
            'DPLEKesimpulanPihak' => 'id_shp_kesimpulan_pihak',
        ];

        $list_shp_peraturan = $this->sp_client
                ->query('DPLEshpPeraturan')
                ->fields(array_keys($select))
                ->where('Peraturan', 'not_null', '')
                ->and_where('DPLEKesimpulanPihak', 'not_null', '');

        $list_shp_peraturan = Helpers::createResults($list_shp_peraturan->get(), $select, ['id_shp_peraturan' => DATA_TYPE_INTEGER, 'id_peraturan' => DATA_TYPE_INTEGER, 'id_shp_kesimpulan_pihak' => DATA_TYPE_INTEGER]);

        if (!count($list_shp_peraturan)) return [];

        return $list_shp_peraturan;
    }

    public function getShpPihak($request) {
        $select = [
            'ID' => 'id_shp_pihak',
            'MasterProfil' => 'id_pihak',
            'DPLEKesimpulanPihak' => 'id_shp_kesimpulan_pihak',
        ];

        $list_shp_pihak = $this->sp_client
                ->query('DPLESHPPihak')
                ->fields(array_keys($select))
                ->where('MasterProfil', 'not_null', '')
                ->and_where('DPLEKesimpulanPihak', 'not_null', '');

        $list_shp_pihak = Helpers::createResults($list_shp_pihak->get(), $select, ['id_shp_pihak' => DATA_TYPE_INTEGER, 'id_pihak' => DATA_TYPE_INTEGER, 'id_shp_kesimpulan_pihak' => DATA_TYPE_INTEGER]);

        if (!count($list_shp_pihak)) return [];

        return $list_shp_pihak;
    }

    public function getTimSuratTugas($request) {
        $select = [
            'ID' => 'id_tim_surat_tugas',
            'SuratTugas' => 'id_surat_tugas',
            'UserAccount' => 'id_user',
        ];

        $list_tim_surat_tugas = $this->sp_client
                ->query('TimSuratTugas')
                ->fields(array_keys($select))
                ->where('PIC', '=', true)
                ->and_where('SuratTugas', 'not_null', '')
                ->and_where('UserAccount', 'not_null', '');

        $list_tim_surat_tugas = Helpers::createResults($list_tim_surat_tugas->get(), $select, ['id_tim_surat_tugas' => DATA_TYPE_INTEGER, 'id_surat_tugas' => DATA_TYPE_INTEGER, 'id_user' => DATA_TYPE_INTEGER]);

        if (!count($list_tim_surat_tugas)) return [];

        return $list_tim_surat_tugas;
    }

    public function getUser($request) {
        $select = [
            'ID' => 'id_user',
            'NamaLengkap' => 'nama_lengkap',
        ];

        $list_user = $this->sp_client
                ->query('UserAccount')
                ->fields(array_keys($select))
                ->where('UserGroup', 'not_null', '')
                ->and_where('NamaLengkap', 'not_null', '');
       
        if ($request->getQueryParam('nama_lengkap')) {
            $list_user = $list_user->and_where('NamaLengkap','contains', $request->getQueryParam('nama_lengkap'));
        }

        $list_user = Helpers::createResults($list_user->get(), $select, ['id_user' => DATA_TYPE_INTEGER]);
        

        if (!count($list_user)) return [];

        return $list_user;
    }
}
