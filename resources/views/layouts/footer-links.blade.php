 <!-- Script Start -->
 <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"
     integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous">
 </script>
 <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"
     integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous">
 </script>
 <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
 <script src="{{ url('plugins/jquery-ui/jquery-ui.min.js') }}"></script>
 <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-toast-plugin/1.3.2/jquery.toast.min.js"></script>
 <script src="https://cdnjs.cloudflare.com/ajax/libs/bootbox.js/5.3.2/bootbox.min.js"></script>

 <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyD996ovxZ7fFt0P5tEjMmdqpoBensOtqBo&libraries=places">
 </script>
 <script src="https://cdnjs.cloudflare.com/ajax/libs/lity/2.4.1/lity.min.js"></script>
 <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/cropper/3.0.0/cropper.min.js"></script> -->
 <script src="{{ url('assets/js/cropper/cropper.js') }}"></script>
 <script src="{{ url('assets/js/cropper/main.js') }}"></script>
 <script src="https://cdn.jsdelivr.net/npm/bootstrap5-toggle@5.0.2/js/bootstrap5-toggle.ecmas.min.js"></script>
 <script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.9/jquery.validate.js"></script>

 <script src="{{ url('assets/js/jsvalidation.js') }}"></script>
 <script src="{{ url('assets/js/app.toast.js') }}"></script>
 <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
 <script src="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/15.5.0/nouislider.min.js"></script>
 <script>
     //$.widget.bridge('uibutton', $.ui.button)
 </script>
 <script src="{{ url('plugins/sparklines/sparkline.js') }}"></script>
 <script src="{{ url('plugins/moment/moment.min.js') }}"></script>
 <script src="https://cdnjs.cloudflare.com/ajax/libs/moment-timezone/0.5.28/moment-timezone-with-data.min.js"></script>
 <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-timepicker/0.5.2/js/bootstrap-timepicker.min.js"></script>
 <script src="{{ url('plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>
 <script src="{{ url('plugins/daterangepicker/daterangepicker.js') }}"></script>
 <script src="{{ url('dist/js/adminlte.js') }}"></script>
 <script src="{{ url('plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') }}"></script>
 <script src="{{ url('plugins/summernote/summernote-bs4.min.js') }}"></script>
 <!-- slick slider -->
 <script src="{{ url('plugins/slick/slick.min.js') }}"></script>
 <script src="{{ url('plugins/nouislider/nouislider.min.js') }}"></script>
 <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.bundle.js"></script>
 <!-- DataTables  & Plugins -->
 <script src="{{ url('../../plugins/datatables/jquery.dataTables.min.js') }}"></script>
 <script src="{{ url('../../plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
 <script src="{{ url('../../plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
 <script src="{{ url('../../plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
 <script src="{{ url('../../plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
 <script src="{{ url('../../plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
 <script src="{{ url('../../plugins/jszip/jszip.min.js') }}"></script>
 <!-- <script src="{{ url('../../plugins/pdfmake/pdfmake.min.js') }}"></script>
 <script src="{{ url('../../plugins/pdfmake/vfs_fonts.js') }}"></script> -->
 <script src="{{ url('../../plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>
 <script src="{{ url('../../plugins/datatables-buttons/js/buttons.print.min.js') }}"></script>
 <script src="{{ url('../../plugins/datatables-buttons/js/buttons.colVis.min.js') }}"></script>
 <script src="{{ url('assets/js/tinymce/tinymce.min.js') }}"></script>
 <script src="{{ url('dist/js/main.js') }}"></script>
 <script src="https://js.stripe.com/v3/"></script>
 <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.9.0/fullcalendar.min.js"></script>
 <!-- <script src="https://cdn.jsdelivr.net/npm/vimeo-jquery-api@0.10.3/dist/jquery.vimeo.api.min.js"></script> -->
 <!-- country code tel -->
 <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
 <script src="https://unpkg.com/sortablejs@latest/Sortable.min.js"></script>
 <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/chartjs-plugin-datalabels/0.7.0/chartjs-plugin-datalabels.min.js">
 </script>  -->
 <!-- Vimeo Player -->
 <script src="https://player.vimeo.com/api/player.js"></script>
 <script src="https://unpkg.com/sortablejs-make/Sortable.min.js"></script>
 <script src="https://cdn.jsdelivr.net/npm/jquery-sortablejs@latest/jquery-sortable.js"></script>
 <script type="text/javascript">
     $.ajaxSetup({
         headers: {
             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
             'time-zone': $('meta[name="user-timezone"]').attr('content') // Use logged-in user's timezone
         }
     });

     function loadHeaderText(type = "") {
         var url = "{{ route('common.getHeaderText') }}";
         $('#headerLoader').show();
         $('#headerLoaderContainer').show();
         $.ajax({
             type: "GET",
             url: url,
             data: {
                 type: type
             },
             success: function(response) {
                 if (response.success) {
                     $('#headerLoader').hide();
                     $('#headerLoaderContainer').hide();
                     $("#textPlaceholder").html("");
                     $('#textPlaceholder').append(response.data.description);
                 }
             },
             error: function() {
                 $('#headerLoader').hide();
                 _toast.error('Somthing went wrong.');
             }
         });
     }

     function displayUpsellMessage(location) {
         $.ajax({
             type: "GET",
             url: "{{ route('common.displayUserUpsell') }}",
             data: {
                 location: location,
             },
             success: function(response) {
                 if (response.success) {
                     $("#upsell-message").html("");
                     $('#upsell-message').append(response.data.html);
                 }
             },
             error: function() {
                 _toast.error('Somthing went wrong.');
             }
         });
     }
 </script>
