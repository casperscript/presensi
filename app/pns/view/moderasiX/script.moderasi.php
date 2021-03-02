<?php
header('application/javascript');
?>
<!--<script>-->
    if (window.innerWidth >= 720) {
        app = {
            init: function (url) {
                url_tabel = url + "/tabelDesktop";
                url_info = url + "/detailModerasiDesktop/";
                url_form = url + "/formDesktop";
                url_hapus = url + "/hapus";
                url_simpan = url + "/simpan";
            },

            loadTabel: function () {
            	$("#progressView").attr("class", "indeterminate");
                $.post(url_tabel, $("#frmData").serialize(), function (data) {
                    $("#progressView").attr("class", "determinate");
                    $("#data-tabel").html(data);
                    
                    $(".tooltipped").tooltip();
                });
        	},

        	infoModerasi: function (id) {
        		$.get(url_info + id, function (result) {
        			$("#modalDetail .btnHapus").attr("id", id);
        			$("#modalDetail .btnEdit").attr("id", id);

        			$("#data-detail").html(result);
                    $("#modalDetail").openModal();
                    $(".tooltipped").tooltip();
        		});
        	},

        	showForm: function (id) {
        		$("#progressView").attr("class", "indeterminate");
        		$.post(url_form, {id: id}, function (result) {
        			$("#progressView").attr("class", "deterimante");
        			$("#modalInput #data-form").html(result);
        			$("#modalInput").openModal();
        			Materialize.updateTextFields();
        		});
        	},

        	showConfirm: function (id, title, msg) {
	            swal({
	                title: title,
	                text: msg,
	                type: "warning",
	                showCancelButton: true,
	                confirmButtonClass: "btn-danger",
	                confirmButtonText: "Hapus data!",
	                closeOnConfirm: false
	            }, function () {
	                $.post(url_hapus, {id: id}, function (response) {
	                    if (response.status === "success") {
	                        swal("Berhasil!", "Data moderasi telah terhapus.", "success");
	                        $("#modalDetail").closeModal();
	                        app.loadTabel();
	                    } else {
	                        swal("Gagal!", "Terjadi kesalahan ketika menghapus, coba lagi.", "warning");
	                    }
	                }, "json");
	            });
	        },

	        simpan: function (obj) {
	        	$("#simpanProgress").attr("class", "indeterminate");
	        	$.post(url_simpan, $(obj).serializeArray(), function (response) {

	        		if (response.status === "success") {
	        			app.loadTabel();
	        			$("#modalInput").closeModal();
	        		}

        			$("#simpanProgress").attr("class", "determinate");
	        		swal(response.title, response.message, response.status);

	        	}, "json");
	    	}

        };
    } else {
    	app = {
    		init: function (url) {
    			url_tabel = url + "/tabelMobile";
    			url_info = url + "/detailModerasiMobile/";
    			url_form = url + "/formMobile";
    			url_simpan = url + "/simpan";
    			url_hapus = url + "/hapus";
    		},

    		loadTabel: function () {
    			$("#progressView").attr("class", "indeterminate")
    			$.post(url_tabel, $("#frmData").serialize(), function (data) {
    				$("#progressView").attr("class", "determinate");
    				$("#data-tabel").html(data);

    				$(".tooltipped").tooltip();
    			});
    		},

    		infoModerasi: function (id) {
    			$.get(url_info + id, function (result) {
	    			$("#modalDetail .btnHapus").attr("id", id);
	    			$("#modalDetail .btnEdit").attr("id", id);

	    			$("#data-detail").html(result);
	    			$("#modalDetail").openModal();
	    			$(".tooltipped").tooltip();
    			});
    		},

        	showForm: function (id) {
        		$("#progressView").attr("class", "indeterminate");
        		$.post(url_form, {id: id}, function (result) {
        			$("#progressView").attr("class", "deterimante");
        			$("#modalInput #data-form").html(result);
        			$("#modalInput").openModal();
        			Materialize.updateTextFields();
        		});
        	},

        	showConfirm: function (id, title, msg) {
	            swal({
	                title: title,
	                text: msg,
	                type: "warning",
	                showCancelButton: true,
	                confirmButtonClass: "btn-danger",
	                confirmButtonText: "Hapus data!",
	                closeOnConfirm: false
	            }, function () {
	                $.post(url_hapus, {id: id}, function (response) {
	                    if (response.status === "success") {
	                        swal("Berhasil!", "Data moderasi telah terhapus.", "success");
	                        $("#modalDetail").closeModal();
	                        app.loadTabel();
	                    } else {
	                        swal("Gagal!", "Terjadi kesalahan ketika menghapus, coba lagi.", "warning");
	                    }
	                }, "json");
	            });
	        },

	        simpan: function (obj) {
	        	$("#simpanProgress").attr("class", "indeterminate");
	        	$.post(url_simpan, $(obj).serializeArray(), function (response) {

	        		if (response.status === "success") {
	        			app.loadTabel();
	        			$("#modalInput").closeModal();
	        		}

        			$("#simpanProgress").attr("class", "determinate");
	        		swal(response.title, response.message, response.status);

	        	}, "json");
	    	}
    	}
	}
