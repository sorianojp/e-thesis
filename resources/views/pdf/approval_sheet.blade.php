<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        @page {
            size: A4;
            margin: 1.5in 1in 1in 1.5in;
        }

        body {
            font-family: "Times New Roman", Times, serif;
            margin: 0;
            font-size: 11pt;
            line-height: 1.7;
        }

        .title {
            text-align: center;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 28px;
        }

        .indent {
            text-indent: 48px;
            text-align: justify;
            margin-bottom: 24px;
        }

        .adviser-block {
            margin-top: 32px;
            text-align: right;
        }

        .center-block {
            text-align: center;
            margin-top: 48px;
        }

        .center-block .name {
            font-weight: bold;
            text-transform: uppercase;
        }

        .highlight {
            text-align: center;
            text-transform: uppercase;
            font-weight: bold;
            margin-top: 32px;
        }

        .dean {
            margin-top: 52px;
            text-align: right;
        }

        .signature-block {
            display: inline-block;
            text-align: center;
        }

        .signature-block .name {
            font-weight: bold;
            text-transform: uppercase;
        }

        .closing {
            margin-top: 32px;
        }
    </style>
</head>

<body>
    <div class="title">Approval Sheet</div>

    <p class="indent">
        In partial fulfillment of the requirements leading to the degree of {{ $courseName ?? '____________' }}, the
        completed
        project study entitled, <span style="font-weight: bold">“{{ $thesis->thesisTitle->title }}”</span>, prepared and submitted by
        <span style="font-weight: bold;  text-transform: uppercase;">{{ $student->name }}</span>, is endorsed for
        approval
        and acceptance.
    </p>

    <div class="adviser-block">
        <div class="signature-block">
            <div class="name">{{ $adviserName ? strtoupper($adviserName) : '_________________________' }}</div>
            <div>Adviser</div>
        </div>
    </div>

    <p class="indent">
        This is to certify that the completed project study mentioned above submitted by <span
            style="font-weight: bold;  text-transform: uppercase;">{{ $student->name }}</span> has been
        examined
        and approved on <span
            style="font-weight: bold;  text-transform: uppercase;">{{ $defenseDate ?? '____________' }}</span> by the
        Oral Examination Committee.
    </p>

    <div class="center-block">
        <div class="name">
            {{ $thesis->thesisTitle->panel_chairman ? strtoupper($thesis->thesisTitle->panel_chairman) : '_________________________' }}</div>
        <div>Chairman</div>
    </div>

    <table style="width: 100%; margin-top: 32px;">
        <tr>
            <td style="text-align: center; width: 50%;">
                <div style="font-weight: bold;">
                    {{ $thesis->thesisTitle->panelist_one ? strtoupper($thesis->thesisTitle->panelist_one) : '_________________________' }}</div>
                <div>Member</div>
            </td>
            <td style="text-align: center; width: 50%;">
                <div style="font-weight: bold;">
                    {{ $thesis->thesisTitle->panelist_two ? strtoupper($thesis->thesisTitle->panelist_two) : '_________________________' }}</div>
                <div>Member</div>
            </td>
        </tr>
    </table>

    <p class="indent closing">
        Accepted and approved as partial fulfillment of the requirements for the degree of
        {{ $courseName ?? '____________' }} on
        <span style="font-weight: bold;  text-transform: uppercase;">{{ $defenseDate ?? '____________' }}</span>, with
        a grade of <span style="font-weight: bold;">_________________________</span>.
    </p>

    <p class="indent closing">Comprehensive Examination: <span style="font-weight: bold;">PASSED</span></p>

    <div class="dean">
        <div class="signature-block">
            <div class="name">CARIDAD OLI ABUAN, Ed.D.</div>
            <div>Dean, School of Professional Studies</div>
        </div>
    </div>
</body>

</html>
