<div class="panel panel-default">
    <div class="panel-heading">
        <h4><i class="fa fa-cutlery"></i> Menus</h4>
    </div>
    <div class="panel-body">

        <div class="col-md-5">
            <div class="form-group showSelector">
                {!! Form::select(null, [], null, ['id' => 'menus', 'class' => 'form-control','disabled']) !!}
            </div>
        </div>
        <div class="col-md-2">
            <div class="btn btn-success btn-block" id="addMenu"><i class="fa fa-plus"></i> Add Menus</div>
        </div>

        <div id="menuList">

       
            @foreach($reservation->reservationMenu as $menu)
                @include('menus::partials.menuLine', ['menu' => $menu->menu, 'menus' => $menu, 'id' => $loop->iteration])
            @endforeach
        </div>
    </div>
</div>

@push('scripts')
    <script type="text/javascript">
      let $menus = $('#menus')
      let id_menu = $('.countMenus').length + 1;
      let $seattype = [];

      function seattypes(value, index, self) { 
        return self.indexOf(value) === index;
      }

       $('body').on('change', 'input.seat-ticket-price', function () {

        $seattype.push($(this).data('seattype'));
         if ($(this).val()) {
           //alert($seattype);
           $menus.removeAttr('disabled');
           var unique = $seattype.filter(seattypes); 

           Menu(unique);

          }

          
      });  


      $(document).ready(function () {
         $menus.select2()
       
        $('#addMenu').click(function () {
          let menus = $menus.val()

          $.post('/addmenu/reservations',
            {menus: menus, id: id_menu}
          ).done(function (datamenu) {
            $('#menuList').append(datamenu)
            id_menu += 1;
          })
        })

        $('body').on('click', '#deleteMenu', function() {
          $(this).parent().parent().parent().remove();
        })
        })
       
      function returnMenu (datamenu) {
        if (datamenu.loading)
          return datamenu.name

        if (datamenu.id)
          return '<b>Menu:</b> ' + datamenu.name 

        return ''
      }

      function Menu ($seattype) {
          $menus.select2('destroy')
          $menus.select2({
            ajax: {
              url: '/list/menus',
              dataType: 'json',
              method: 'POST',
              delay: 250,
              data: {seattype: $seattype},
              success: function (params) {
                return {
                  
                  q: $seattype,
                  s: params
                }
              },
              processResults: function (datamenu) {
                return {
                  results: datamenu
                  
                }
              }
            },
            escapeMarkup: function (markup) { return markup }, // let our custom formatter work
            templateResult: returnMenu,
            templateSelection: returnMenu,
          })
       }

            //Límites para Menus.
    $('body').on('change', '#menuList .quantity', function () {
        var $this = $(this);
        var val = total = 0;

        $('#menuList .quantity').each(function (k, v) {
            val += parseInt($(this).val());
        });

        $('input.seat-ticket-price').each(function (k, v) {
            total += parseInt($(this).val());
        });

        if (total < val) {
           
             swal({
                        title: "There are only " + total + " seats availables",
                        confirmButtonColor: "#d9534f",
                        closeOnConfirm: true,
                        animation: "slide-from-top"
                    });
            $this.val(0);
        }
    });
    </script>
@endpush