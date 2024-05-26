<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Verification code to confirm your email address</title>
  <!--[if mso]><style type="text/css">body, table, td, a { font-family: Arial, Helvetica, sans-serif !important; }</style><![endif]-->
</head>

<?php 
  $buttonStyle = 'display: inline-block;
    font-weight: 400;
    text-align: center;
    white-space: nowrap;
    vertical-align: middle;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
    border: 1px solid transparent;
    border-top-color: transparent;
    border-right-color: transparent;
    border-bottom-color: transparent;
    border-left-color: transparent;
    padding: .375rem .75rem;
    font-size: 1rem;
    line-height: 1.5;
    border-radius: .25rem;
    transition: color .15s ease-in-out,background-color .15s ease-in-out,border-color .15s ease-in-out,box-shadow .15s ease-in-out;
    border-radius: 15px;
    padding: 0.5rem 3rem;
    text-decoration:none;';
?>
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
                      <h1 style="margin: 1rem 0; text-align:center;">{{ ucfirst($translations['we have some news about a job you applied']) }}!</h1>
                      <p style="margin: 1rem 0; padding-top: 2rem; text-align:center;">
                        {{ ucfirst($translations['the job offer']) }}
                        {{ ucwords($jobName) }} 
                        {{ $translations['from the company'] }}
                        {{ ucwords($companyName) }}
                      </p>
                      <p style="padding-bottom: 16px; text-align:center"><em>{{ ucfirst($translations['you appliance status has changed']) }}</em></p>
                      <p style="text-align:center;">
                        @switch($applicationStatus)
                            @case(\App\Models\JobApplied::STATUS_APPLIED)
                              <a style="padding: 1rem; background: #5a6268; color: #fff; {{ $buttonStyle }}">{{ mb_strtoupper($statusTranslation) }}</a>
                            @break
                            @case(\App\Models\JobApplied::STATUS_VALIDATION)
                              <a style="padding: 1rem; background: #138496; color: #fff; {{ $buttonStyle }}">{{ mb_strtoupper($statusTranslation) }}</a>
                            @break
                            @case(\App\Models\JobApplied::STATUS_REFUSED)
                              <a style="padding: 1rem; background: #dc3545; color: #fff; {{ $buttonStyle }}">{{ mb_strtoupper($statusTranslation) }}</a>
                            @break
                            @case(\App\Models\JobApplied::STATUS_ACCEPTED)
                              <a style="padding: 1rem; background: #218838; color: #fff; {{ $buttonStyle }}">{{ mb_strtoupper($statusTranslation) }}</a>
                            @break
                                
                        @endswitch
                      </p>
                      <div style="text-align: center;">
                        <p style="margin-top: 4rem; font-size: 15px">
                          {{ $translations['see notification and details on your notifications page or click in the button below'] }}:
                        </p>
                        <a style="{{ $buttonStyle }}; background:#e2e6ea; color:#212529" href="{{ url('notification/index') }}" target="_blank">{{ $translations['see notification'] }}</a>
                      </div>
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