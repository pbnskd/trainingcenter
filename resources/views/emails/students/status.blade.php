<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>Academic Status Update</title>
    <style type="text/css">
        body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
        table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
        img { -ms-interpolation-mode: bicubic; }
        body { height: 100% !important; margin: 0; padding: 0; width: 100% !important; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f1f5f9; color: #334155; }
        a { text-decoration: none; }
        @media screen and (max-width: 600px) {
            .email-container { width: 100% !important; }
            .content-cell { padding: 20px !important; }
            .responsive-table td { display: block; width: 100% !important; padding: 10px !important; }
        }
    </style>
</head>
<body style="margin: 0; padding: 0; background-color: #f1f5f9;">

<table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f1f5f9; padding: 40px 0;">
    <tr>
        <td align="center">
            
            <table border="0" cellpadding="0" cellspacing="0" width="600" class="email-container" style="background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.05); width: 600px; max-width: 600px;">
                
                {{-- HEADER --}}
                <tr>
                    <td align="center" style="padding: 40px 40px 30px 40px; border-bottom: 1px solid #f1f5f9;">
                        <div style="font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 10px;">
                            {{ config('app.name') }}
                        </div>
                        
                        <h1 style="margin: 0 0 10px 0; font-size: 24px; font-weight: 800; color: #0f172a; line-height: 1.2;">
                            @if($student->academic_status === 'enrolled') Academic Enrollment
                            @elseif($student->academic_status === 'graduated') 🎉 Congratulations!
                            @elseif($student->academic_status === 'suspended') ⚠️ Academic Notice
                            @elseif($student->academic_status === 'alumni') Welcome to Alumni
                            @else Status Update
                            @endif
                        </h1>

                        <div style="display: inline-block; background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 6px 12px; font-family: monospace; font-size: 14px; color: #475569; font-weight: 600;">
                            ID: {{ $student->registration_number }}
                        </div>
                    </td>
                </tr>

                {{-- CONTENT BODY --}}
                <tr>
                    <td class="content-cell" style="padding: 30px 40px;">

                        <p style="margin: 0 0 25px 0; font-size: 16px; color: #334155; line-height: 1.6;">
                            Hello <strong>{{ ucwords($student->user->name) }}</strong>,
                        </p>

                        {{-- STATUS BADGE LOGIC --}}
                        @php
                            $st = strtolower($student->academic_status);
                            
                            // Color Logic based on academic status
                            $badgeBg = '#f1f5f9'; $badgeTxt = '#475569';
                            if($st === 'enrolled') { $badgeBg = '#eff6ff'; $badgeTxt = '#1d4ed8'; } 
                            elseif($st === 'alumni') { $badgeBg = '#faf5ff'; $badgeTxt = '#7e22ce'; }
                            elseif($st === 'graduated') { $badgeBg = '#f0fdf4'; $badgeTxt = '#15803d'; }
                            elseif($st === 'suspended') { $badgeBg = '#fef2f2'; $badgeTxt = '#b91c1c'; }
                        @endphp

                        <table width="100%" cellpadding="12" cellspacing="0" style="border: 1px solid #e2e8f0; border-radius: 8px; margin-bottom: 30px; border-collapse: separate; overflow: hidden;">
                            <tr>
                                <td width="50%" style="border-right: 1px solid #e2e8f0; background-color: #f8fafc;">
                                    <span style="display: block; font-size: 10px; font-weight: 700; text-transform: uppercase; color: #64748b; letter-spacing: 0.5px; margin-bottom: 4px;">Date of Update</span>
                                    <strong style="color: #0f172a; font-size: 15px;">
                                        {{ now()->format('M d, Y') }}
                                    </strong>
                                </td>
                                <td width="50%" style="background-color: #f8fafc;">
                                    <span style="display: block; font-size: 10px; font-weight: 700; text-transform: uppercase; color: #64748b; letter-spacing: 0.5px; margin-bottom: 4px;">Current Status</span>
                                    <span style="background-color: {{ $badgeBg }}; color: {{ $badgeTxt }}; padding: 3px 8px; border-radius: 4px; font-size: 11px; font-weight: 700; text-transform: uppercase;">
                                        {{ str_replace('_', ' ', $st) }}
                                    </span>
                                </td>
                            </tr>
                        </table>

                        {{-- DYNAMIC SCENARIOS --}}

                        @if($student->academic_status === 'enrolled')
                            <div style="background-color: #eff6ff; border-left: 4px solid #3b82f6; padding: 15px; margin-bottom: 20px;">
                                <strong style="color: #1e40af;">📚 Active & Enrolled</strong>
                                <p style="margin: 5px 0 0 0; color: #1e3a8a; font-size: 14px; line-height: 1.5;">You are currently enrolled and in good academic standing. We are thrilled to have you studying with us.</p>
                            </div>
                            
                            <p style="font-size: 14px; font-weight: bold; color: #334155; margin-bottom: 10px;">Your Next Steps:</p>
                            <ul style="margin: 0 0 20px 0; padding-left: 20px; color: #475569; font-size: 14px; line-height: 1.6;">
                                <li>Access your course materials and latest syllabus.</li>
                                <li>Check your upcoming batch schedules and class timings.</li>
                                <li>Connect with your instructors and peers.</li>
                            </ul>

                        @elseif($student->academic_status === 'graduated')
                            <div style="text-align: center; padding: 10px 0 20px 0;">
                                <div style="font-size: 40px; margin-bottom: 10px;">🎓</div>
                                <h2 style="margin: 0; color: #0f172a; font-size: 18px;">You did it!</h2>
                                <p style="color: #64748b; margin-top: 5px; font-size: 14px;">We are incredibly proud of your hard work and dedication to reach this milestone.</p>
                            </div>

                            <p style="font-size: 14px; font-weight: bold; color: #334155; margin-bottom: 10px;">Your Next Steps:</p>
                            <ul style="margin: 0 0 20px 0; padding-left: 20px; color: #475569; font-size: 14px; line-height: 1.6;">
                                <li>Ensure your mailing address is updated to receive your official certificate.</li>
                                <li>Look out for our upcoming graduation ceremony details.</li>
                                <li>Explore our career placement resources.</li>
                            </ul>

                        @elseif($student->academic_status === 'suspended')
                            <div style="background-color: #fef2f2; border-left: 4px solid #ef4444; padding: 15px; margin-bottom: 20px;">
                                <strong style="color: #991b1b;">⚠️ Notice of Suspension</strong>
                                <p style="margin: 5px 0 0 0; color: #7f1d1d; font-size: 14px; line-height: 1.5;">Your account access and participation in active batches have been temporarily paused.</p>
                            </div>

                            <p style="font-size: 14px; font-weight: bold; color: #334155; margin-bottom: 10px;">Required Actions:</p>
                            <ul style="margin: 0 0 20px 0; padding-left: 20px; color: #475569; font-size: 14px; line-height: 1.6;">
                                <li>Review the student handbook regarding academic and behavioral policies.</li>
                                <li>Contact the administration office immediately to discuss your case.</li>
                            </ul>

                        @elseif($student->academic_status === 'alumni')
                            <div style="background-color: #faf5ff; border-left: 4px solid #a855f7; padding: 15px; margin-bottom: 20px;">
                                <strong style="color: #6b21a8;">🤝 Welcome to the Alumni Network</strong>
                                <p style="margin: 5px 0 0 0; color: #581c87; font-size: 14px; line-height: 1.5;">Thank you for being a valued part of our institution's ongoing history.</p>
                            </div>

                            <p style="font-size: 14px; font-weight: bold; color: #334155; margin-bottom: 10px;">Your Next Steps:</p>
                            <ul style="margin: 0 0 20px 0; padding-left: 20px; color: #475569; font-size: 14px; line-height: 1.6;">
                                <li>Access exclusive alumni resources and job boards.</li>
                                <li>Stay connected for networking events and industry webinars.</li>
                            </ul>

                        @endif

                        {{-- CALL TO ACTION BUTTON --}}
                        <table width="100%" cellpadding="0" cellspacing="0" style="margin-top: 30px;">
                            <tr>
                                <td align="center">
                                    <a href="{{ route('login') }}" 
                                       style="background-color: #0f172a; color: #ffffff; padding: 12px 24px; border-radius: 6px; font-weight: 600; display: inline-block; font-size: 14px; text-decoration: none;">
                                        Access Student Portal
                                    </a>
                                </td>
                            </tr>
                        </table>

                    </td>
                </tr>

                {{-- FOOTER --}}
                <tr>
                    <td style="background-color: #f8fafc; padding: 20px; text-align: center; border-top: 1px solid #e2e8f0;">
                        <p style="font-size: 12px; color: #94a3b8; line-height: 1.5; margin: 0;">
                            <strong>Need Assistance?</strong> If you believe this status update is an error, please reach out to our academic support team.<br><br>
                            &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
                        </p>
                    </td>
                </tr>

            </table>
            
        </td>
    </tr>
</table>

</body>
</html>