CREATE TABLE `calon_siswa` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `nama` VARCHAR(64) NOT NULL,
  `alamat` VARCHAR(255) NOT NULL,
  `jenis_kelamin` VARCHAR(16) NOT NULL,
  `agama` VARCHAR(16) NOT NULL,
  `sekolah_asal` VARCHAR(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
