<?xml version="1.0" encoding="Shift_JIS"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja" lang="ja">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=Shift_JIS" />
<meta http-equiv="Content-Language" content="ja" />
<meta http-equiv="Content-Style-Type" content="text/css" />
<meta http-equiv="Content-Script-Type" content="text/javascript" />
<title>入力画面</title>
<link href="./static/css/default.css" type="text/css" rel="stylesheet" />
<script type="text/javascript" src="./static/js/form.js"></script>
</head>
<body>

<div id="mp-main">
<!-- エラー部 start -->
<TMPL_IF NAME="errs"><div class="errs"><ul><TMPL_LOOP NAME="err_loop"><li>%err%</li></TMPL_LOOP></ul></div></TMPL_IF>
<!-- エラー部 end -->
<!-- 入力部 start -->
%form%
<!-- 入力部 end -->
</div>
</body>
</html>