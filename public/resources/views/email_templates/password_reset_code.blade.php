<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ ucfirst(translate('reset password')) }}</title>
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
                  <div style="padding: 20px; background-color: rgb(255, 255, 255);text-align:center;">
                    <div style="color: rgb(0, 0, 0); text-align:center;">
                      <h1 style="margin:1rem 0;padding-bottom:5px;border-bottom:1px solid rgb(153, 153, 153);">{{ ucfirst(translate('reset password')) }}</h1>
                      <p style="padding-top: 10px">
                        {{ ucfirst(translate('hi')) }} {{ $personName }},
                      </p>
                      <p>
                        {{ ucfirst(translate('forgot your password?')) }}
                      </p>
                      <p>
                        {{ ucfirst(translate('we received a request to reset the password for your account')) }}.
                      </p>
                      <p>
                        {{ ucfirst(translate('to reset your password, click the button below')) }}:
                      </p>
                      <p>
                        <a 
                          style="display:inline-block;font-weight:400;color:rgb(255 255 255/1);text-align:center;border:1px solid transparent;
                          font-size: 1rem;line-height:1.5;border-radius: .25rem;background:rgb(83 195 196/1);text-decoration:none;padding:10px;" 
                          href="{{ $url }}">
                            {{ ucfirst(translate('reset password')) }}                    
                        </a>
                      </p>
                      <p style="padding-top: 15px;">
                        {{ ucfirst(translate('or copy and paste the URL into your browser')) }}:
                      </p>
                      <p style="text-decoration: underline; color: blue;">
                        {{ $url }}
                      </p>

                      <p style="padding-top: 20px">
                        {{ ucfirst(translate('in case you did not request this email, please ignore')) }}.
                      </p>
                      <p">
                        {{ ucfirst(translate('thanks')) }}.
                      </p>
                      <div style="padding-top: 5px; color: rgb(153, 153, 153); text-align: center;">
                        <p style="padding-bottom: 16px; font-weight:600;">{{ ucfirst(translate('team')) }} <span style="color:#0b0b55;">Job<span style="color:#2adffd;">i</span>Full</span></p>
                      </div>
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