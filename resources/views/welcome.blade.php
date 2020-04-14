<!DOCTYPE html>
<?php header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache"); ?>
<html lang="en">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <!-- Meta, title, CSS, favicons, etc. -->
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="Check link web pages">
  <meta name="keywords" content="checkLinks">
  <meta name="author" content="Yang Qing Gui">
  <meta name="_token" id="_token" content="{{ csrf_token() }}">
  <title>Kiểm tra links</title>
  <base href="{{asset('')}}>">
  <!-- Bootstrap -->
  <link rel="shortcut icon" href="resources/img/_favicon.ico" type="image/ico" media="all" />
  <link href="node_modules/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet" media="all"/>
  <!-- NProgress -->
  <link href="node_modules/nprogress/nprogress.css" rel="stylesheet" media="all"/>
  <!-- Datatables -->
  <link href="node_modules/datatables.net-bs4/css/dataTables.bootstrap4.min.css" rel="stylesheet" media="all">
  <link href="node_modules/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css" rel="stylesheet" media="all">

</head>

<body>
<div class="container">
  <!-- Content here -->
  <div class="row" style=" margin-top: 100px">
    <div class="col-xl-2 col-md-2 col-sm-2"></div>
    <div class="col-xl-8 col-md-8 col-sm-8">
      <form class="form-inline">
            <div class="form-group mx-sm-3 mb-2">
              <label for="urlCheck" class="mr-2">URL </label>
              <input type="text" class="form-control" id="txtUrlCheck" placeholder="https://vietvang.net">
            </div>
            <button type="button" class="btn btn-primary mb-2" id="btnCheck">Check</button>
      </form>
    </div>
    <div class="col-xl-2 col-md-2 col-sm-2"></div>
  </div>
  <div style="border: thin solid grey; margin: 5px 0"></div>
  <div class="row" style=" margin-top: 15px">
    <div class="col-xl-2 col-md-2 col-sm-2">
        <label>Summary</label>
    </div>
    <div class="col-xl-8 col-md-8 col-sm-8">
        <table id="tblSummary" class="table table-striped table-bordered" style="width:100%">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Response status</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
    </div>
      <div style="border: thin solid grey; margin: 5px 0"></div>
    <div class="row" style=" margin-top: 15px">
    <div class="col-xl-2 col-md-2 col-sm-2">
        <label>Details</label>
    </div>
    <div class="col-xl-8 col-md-8 col-sm-8">
        <table id="tblDetails" class="table table-striped table-bordered" style="width:100%">
            <thead>
                <tr>
                    <th>#</th>
                    <th>URL</th>
                    <th>Response status</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
    <div class="col-xl-2 col-md-2 col-sm-2"></div>
  </div>
</div>
  
  <!-- jQuery -->
  <script src="node_modules/jquery/dist/jquery.min.js"></script>
  <!-- Bootstrap -->
  <script src="node_modules/bootstrap/dist/js/bootstrap.min.js"></script>  
  <!-- NProgress -->
  <script src="node_modules/nprogress/nprogress.js"></script>
  <!-- Datatables -->
  <script src="node_modules/datatables.net/js/jquery.dataTables.min.js"></script>
  <script src="node_modules/datatables.net-bs4/js/dataTables.bootstrap4.js"></script>
  <script>
    if (typeof NProgress !== typeof undefined) {
      NProgress.start();
    }
    $(function(){
        $(window).on('load', function(){
            NProgress.done();
        });
        $('#tblDetails').DataTable();
        $('#tblSummary').DataTable();
        $('#btnCheck').on('click', function(){
            try
            {
                var txtUrlCheck = $('#txtUrlCheck').val();
                var formData = null;

                if(!txtUrlCheck) throw new TypeError("Bạn phải nhập URL!");
                NProgress.start();
                formData = new FormData();
                formData.append('_token', $('#_token').attr('content'));
                formData.append('_url', txtUrlCheck);
                $.ajax({
                    url: '{{route("checkUrl")}}',
                    type: 'POST',
                    dataType: 'JSON',
                    xhr: function(){return $.ajaxSettings.xhr();},
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: formData,
                    success: function(data)
                    {
                        try
                        {
                            var i = 0;
                            var j = 0;
                            var k = 0;
                            var count = 0;
                            var dtbDetails = $('#tblDetails');
                            var dtbSummary = $('#tblSummary');
                            if(data.flag)
                            {
                                dtbDetails.dataTable().fnClearTable();
                                dtbSummary.dataTable().fnClearTable();
                                for(; i < data.urls.length; ++i)
                                {
                                    dtbDetails.dataTable().fnAddData([(i + 1), '<a target="_blank" href="' + data.urls[i].url + '">' + data.urls[i].url + '</a>', data.urls[i].status]);
                                    if(!i && (data.urls[i].status === data.urls[j].status) && (i === data.urls.length - 1))
                                        dtbSummary.dataTable().fnAddData([++k, data.urls[i].status, 1]);
                                    else if(data.urls[i].status === data.urls[j].status && (i === data.urls.length - 1))
                                        dtbSummary.dataTable().fnAddData([++k, data.urls[i].status, (i - count) + 1]);
                                    else if(data.urls[i].status !== data.urls[j].status)
                                    {
                                        if(i === data.urls.length - 1)
                                        {
                                            dtbSummary.dataTable().fnAddData([++k, data.urls[j].status, i - count]);
                                            dtbSummary.dataTable().fnAddData([++k, data.urls[i].status, 1]);
                                        }
                                        else
                                        {
                                            dtbSummary.dataTable().fnAddData([++k, data.urls[j].status, i - count]);
                                            count = i;
                                        }
                                    }
                                    j = i;
                                }
                                NProgress.done();
                            }
                            else
                                throw new TypeError("Lỗi: " + data.error);
                        }
                        catch(e)
                        {
                            alert(e.stack);
                        }
                        
                    },
                    error: function(jqXHR, textStatus, errorThrown)
                    {
                        if(jqXHR.status == 419)
                            throw new TypeError('Người dùng không được xác thực (có thể đã đăng xuất hoặc có thể do cookie hoặc session đã bị xoá). ' + jqXHR.responseText + '. ' + textStatus + '. ' + errorThrown);
                        else if(jqXHR.status == 500)
                            throw new TypeError('Đã phát hiện lỗi trên máy chủ phục vụ. ' + jqXHR.responseText + '. ' + textStatus + '. ' + errorThrown);
                        else
                            throw new TypeError('Lỗi server. ' + jqXHR.responseText + '. ' + textStatus + '. ' + errorThrown);
                    }
                });
            }
            catch(e)
            {
                alert(e.stack);
            }
            return;
        });
    });
  </script>
</body>
</html>
