━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
■受付内容
──────────────────────────────────

▼受付シリアル番号
%SERIAL%

▼お問い合わせ事項
<TMPL_LOOP NAME="otoiawase_jikou_element_loop">%element%
</TMPL_LOOP>

▼お名前
%name%

▼お名前（フリガナ）
%name_furigana%

▼郵便番号
%add_yuubin%

▼ご住所
%add%

▼メールアドレス
%email%

▼メールアドレス（再入力）
%email02%

▼お問い合わせ
%comment%



━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
■送信者情報
──────────────────────────────────
・送信日時　　　　：%RECEPTION_DATE_Y%-%RECEPTION_DATE_m%-%RECEPTION_DATE_d% %RECEPTION_DATE_H%:%RECEPTION_DATE_i%:%RECEPTION_DATE_s% %RECEPTION_DATE_O%<TMPL_IF NAME="RECEPTION_DATE_e"> %RECEPTION_DATE_e%</TMPL_IF><TMPL_IF NAME="RECEPTION_DATE_I">（夏時間）</TMPL_IF>
・送信元IPアドレス：%REMOTE_ADDR%
・送信元ホスト名　：%REMOTE_HOST%
・ブラウザー　　　：%HTTP_USER_AGENT%