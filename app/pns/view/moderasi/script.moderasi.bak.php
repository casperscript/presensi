/* ============================= *
			        var xhr = new XMLHttpRequest();
		            var formData = new FormData();
		            var file = $("#lampiran").get(0).files[0];
		            if (file !== undefined) {
		                if (file.size > 2097152) {
		                    alert("File gambar tidak boleh lebih dari 2mb");
		                    return false;
		                } else {
		                    formData.append("lampiran", $("#lampiran").val());
		                    formData.append("file", file);
		                }
		            }

		            formData.append("id", $("#id").val());
		            formData.append("kd_jenis", $("#kd_jenis").val());
		            formData.append("kode_presensi", $("#kode_presensi").val());
		            formData.append("tanggal_awal", $("#tanggal_awal").val());
		            formData.append("tanggal_akhir", $("#tanggal_akhir").val());
		            formData.append("keterangan", $("#keterangan").val());
		            xhr.open("POST", "<?= $url_path ?>/simpan");
		            xhr.onreadystatechange = function () {
		                if (xhr.readyState === 4) {
		                    var response = JSON.parse(xhr.responseText);
		                    app.loadTabel();
		                    swal({
		                        title: response.title,
		                        text: response.text,
		                        type: response.type,
		                        confirmButtonClass: "btn-primary",
		                        confirmButtonText: "OK"
		                    },
                            function () {
                                $("#modalInput").closeModal();
                            });
		                }
		            };
		            xhr.send(formData);
			        /* ============================= */