<?
    /* ============================================================================== */
    /* =   PAGE : ��� ó�� PAGE                                                    = */
    /* = -------------------------------------------------------------------------- = */
    /* =   pp_cli_hub.php ���Ͽ��� ó���� ������� ����ϴ� �������Դϴ�.           = */
    /* = -------------------------------------------------------------------------- = */
    /* =   ������ ������ �߻��ϴ� ��� �Ʒ��� �ּҷ� �����ϼż� Ȯ���Ͻñ� �ٶ��ϴ�.= */
    /* =   ���� �ּ� : http://kcp.co.kr/technique.requestcode.do                    = */
    /* = -------------------------------------------------------------------------- = */
    /* =   Copyright (c)  2016   NHN KCP Inc.   All Rights Reserverd.               = */
    /* ============================================================================== */
?>
<?
    /* ============================================================================== */
    /* =   ���� ���                                                                = */
    /* = -------------------------------------------------------------------------- = */
    $res_cd           = $_POST[ "res_cd"         ];      // ����ڵ�
    $res_msg          = $_POST[ "res_msg"        ];      // ����޽���
	/* ============================================================================== */
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" >

<head>
    <title>*** NHN KCP [AX-HUB Version] ***</title>
    <meta http-equiv="Content-Type" content="text/html; charset=euc-kr" />
    <link href="../sample/css/style.css" rel="stylesheet" type="text/css" id="cssLink"/>
</head>

<body>
    <div id="sample_wrap">
        <h1>[������]<span> �� �������� ���� ����� ����ϴ� ����(����) �������Դϴ�.</span></h1>
     <div class="sample">
        <p>
          ��û ����� ����ϴ� ������ �Դϴ�.<br />
          ��û�� ���������� ó���� ��� ����ڵ�(res_cd)���� 0000���� ǥ�õ˴ϴ�.
        </p>
<?
    /* ============================================================================== */
    /* =   ���� ��� �ڵ� �� �޽��� ���(����������� �ݵ�� ������ֽñ� �ٶ��ϴ�.)= */
    /* = -------------------------------------------------------------------------- = */
    /* =   ���� ���� : res_cd���� 0000���� �����˴ϴ�.                              = */
    /* =   ���� ���� : res_cd���� 0000�̿��� ������ �����˴ϴ�.                     = */
    /* = -------------------------------------------------------------------------- = */
?>
                    <h2>&sdot; ó�� ���</h2>
                    <table class="tbl" cellpadding="0" cellspacing="0">
                        <!-- ��� �ڵ� -->
                        <tr>
                          <th>��� �ڵ�</th>
                          <td><?=$res_cd?></td>
                        </tr>
                              <!-- ��� �޽��� -->
                        <tr>
                          <th>��� �޼���</th>
                          <td><?=$res_msg?></td>
                        </tr>
                 </table>
<?
    /* = -------------------------------------------------------------------------- = */
    /* =   ���� ��� �ڵ� �� �޽��� ��� ��                                         = */
    /* ============================================================================== */
?>
                    <!-- ���� ��û/ó������ �̹��� ��ư -->
                <tr>
                <div class="btnset">
                <a href="../index.html" class="home">ó������</a>
                </div>
                </tr>
              </tr>
            </div>
        <div class="footer">
                Copyright (c) NHN KCP INC. All Rights reserved.
        </div>
    </div>
</body>
</html>