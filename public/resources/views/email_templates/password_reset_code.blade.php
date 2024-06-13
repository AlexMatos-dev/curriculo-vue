<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ ucfirst($translations['verification code to change your password']) }}</title>
  <!--[if mso]><style type="text/css">body, table, td, a { font-family: Arial, Helvetica, sans-serif !important; }</style><![endif]-->
</head>

<body style="font-family: Helvetica, Arial, sans-serif; margin: 0px; padding: 0px; background-color: #ffffff;">
  <table role="presentation"
    style="width: 100%; border-collapse: collapse; border: 0px; border-spacing: 0px; font-family: Arial, Helvetica, sans-serif; background-color: rgb(239, 239, 239);">
    <tbody>
      <tr>
        <td align="center" style="padding: 1rem 2rem; vertical-align: top; width: 100%;">
          <table role="presentation" style="max-width: 600px; border-collapse: collapse; border: 0px; border-spacing: 0px; text-align: left;">
            <tbody>
              <tr>
                <td style="padding: 40px 0px 0px;">
                  <div style="text-align: left;">
                    <div style="padding-bottom: 20px;"><img src="data:image/webp;base64,{{ base64_encode(file_get_contents(public_path('images/logo.webp'))) }}" alt="System logo" style="width: 56px;"></div>
                  </div>
                  <div style="padding: 20px; background-color: rgb(255, 255, 255);">
                    <div style="color: rgb(0, 0, 0); text-align: left;">
                      <h1 style="margin: 1rem 0">{{ ucfirst($translations['your change password code']) }}:</h1>
                      <p style="padding-bottom: 16px"><span style="text-align: center;display: block;"><strong
                            style="font-size: 130%">{{ $code }}</strong></span></p>
                      <p style="padding-bottom: 16px"><em>{{ ucfirst($translations['the code will expire in 30 minutes and can be used only once']) }}.</em></p>
                      <p style="padding-bottom: 16px">{{ ucfirst($translations['in case you did not request this email, please ignore']) }}.</p>
                      <p style="padding-bottom: 16px">{{ ucfirst($translations['thanks']) }}.</p>
                    </div>
                  </div>
                  <div style="padding-top: 20px; color: rgb(153, 153, 153); text-align: center;">
                    <p style="padding-bottom: 16px; font-weight:600;">{{ $translations['team'] }} <span style="color:#0b0b55;">Job<span style="color:#2adffd;">i</span>Full</span></p>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </td>
      </tr>
    </tbody>
  </table>
</body>

</html>