
<script>
    $(document).ready(function () {
        var url = "<?= base_url('report/manage'); ?>";
        //var url_print = "<?= base_url('report/prints'); ?>"; 
        var url_print_trans = "<?= base_url('transaksi/print'); ?>";
        var url_print = "<?= base_url('report'); ?>";
        
        $(function () {
            //DatePicker
            $("#start, #end").datepicker({
                // defaultDate: new Date(),
                format: 'dd-mm-yyyy',
                autoclose: true,
                enableOnReadOnly: true,
                language: "en",
                todayHighlight: true,
                weekStart: 1
            }).on("changeDate", function (e) { 
                
            });
        });
        
        $('#filter_kontak').select2({
            //dropdownParent:$("#modal-id"), //If Select2 Inside Modal
            placeholder: '<i class="fas fa-search"></i> Search',
            minimumInputLength: 0,
            ajax: {
                type: "get",
                url: "<?= base_url('search/manage'); ?>",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    var query = {
                        search: params.term,
                        tipe: 3, //1=Supplier, 2=Asuransi
                        source: 'contacts'
                    }
                    return query;
                },
                processResults: function (datas, params) {
                    params.page = params.page || 1;
                    return {
                        results: datas,
                        pagination: {
                            more: (params.page * 10) < datas.count_filtered
                        }
                    };
                },
                cache: true
            },
            escapeMarkup: function (markup) {
                return markup;
            },
            templateResult: function (datas) { //When Select on Click
                if (!datas.id) {
                    return datas.text;
                }
                if ($.isNumeric(datas.id) == true) {
                    // return '<i class="fas fa-user-check '+datas.id.toLowerCase()+'"></i> '+datas.text;
                    return datas.text;
                }
            },
            templateSelection: function (datas) { //When Option on Click
                if (!datas.id) {
                    return datas.text;
                }
                //Custom Data Attribute
                $(datas.element).attr('data-alamat', datas.alamat);
                $(datas.element).attr('data-telepon', datas.telepon);
                $(datas.element).attr('data-email', datas.email);
                if ($.isNumeric(datas.id) == true) {
                    return '<i class="fas fa-user-check ' + datas.id.toLowerCase() + '"></i> ' + datas.text;
                }
            }
        });  

        $(document).on("click", ".btn-print-all", function () {
            var id = $(this).attr("data-id");
            var action = $(this).attr('data-action'); //1,2
            var request = $(this).attr('data-request'); //report_purchase_buy_recap
            var format = $(this).attr('data-format'); //html, xls
            var contact = $("#filter_kontak").find(':selected').val();
            var ftype = $("#filter_type").find(':selected').val();

            var order = $("#filter_order").find(':selected').val();
            if (order == 0) {
                order = 'att_date_created';
            } else if (order == 1) {
                order = 'att_location_id';
            } else if (order == 3) {
                order = 'att_user_id';
            }

            var dir = $("#filter_dir").find(':selected').val();
            //alert('#btn-print-all on Click'+action+','+request);
            // var x = screen.width / 2 - 700 / 2;
            // var y = screen.height / 2 - 450 / 2;
            // var print_url = url_print +'/'+ action + '/' +request+ '/' +contact+ '/' + $("#start").val() + '/' + $("#end").val();
            var print_url = url_print + '/'
                    + request + '/'
                    + $("#start").val() + '/'
                    + $("#end").val() + '/'
                    + contact + "?format=" + format + "&order=" + order + "&dir=" + dir + "&type=" + ftype;
            window.open(print_url, '_blank');
            // var request = $('.btn-print-all').data('request');
            // var print_url = url_print +'/'+ request + '/'+ $("#start").val() +'/'+ $("#end").val();
            // var win = window.open(print_url,'Print','width=700,height=485,left=' + x + ',top=' + y + '').print();   
        });
        // Print Button
        // $(document).on("click", ".btn-print", function () {
        //     // var id = $(this).attr("data-id");
        //     var id = $(this).attr('data-session');
        //     if (id) {
        //         var x = screen.width / 2 - 700 / 2;
        //         var y = screen.height / 2 - 450 / 2;
        //         var print_url = url_print_trans + '/' + id;
        //         // console.log(print_url);
        //         var win = window.open(print_url, 'Print', 'width=700,height=485,left=' + x + ',top=' + y + '').print();
        //         var data = id;
        //         // $.post(url_print, {id:data}, function (data) {
        //         //     var w = window.open(print_url,'Print');
        //         //     w.document.open();
        //         //     w.document.write(data);
        //         //     w.document.close();
        //         // });
        //     } else {
        //         notif(0, 'Dokumen belum di buka');
        //     }
        // });
    });
</script>