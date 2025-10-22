<?php

use Illuminate\Support\Arr;

$totals = [
    'factDays' => 0,
    'fact' => 0,
    'overtime' => 0,
    'sickLeave' => 0,
    'laborLeave' => 0,
    'freeVacation' => 0,
    'wasnts' => 0,
    'businessTrip' => 0,
    'celebrations' => 0,
];
?>
<tr>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td style="font-weight: bold; font-size: 14px;" colspan="13">Утверждаю:</td>
</tr>
<tr>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td style="font-weight: bold; font-size: 14px; text-align: right; border: 1px solid #ffffff" colspan="4">Директор
    </td>
    <td style="font-size: 8px; text-decoration: underline; font-style: italic; color:#717171;" colspan="8">(наименование
        компании)_____________________
    </td>
</tr>
<tr></tr>
<tr>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td style="font-size: 8px; text-decoration: underline; font-style: italic; color:#717171;" colspan="8">
        (подпись)_____________________
    </td>
    <td style="font-size: 8px; text-decoration: underline; font-style: italic; color:#717171;" colspan="8">
        (ФИО)_____________________
    </td>
</tr>
<tr></tr>
<tr>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td style="font-size: 8px; text-decoration: underline; font-style: italic; color:#717171;" colspan="8">
        (дата)_____________________
    </td>
</tr>

<tr style="border: 1px solid black;">
    <td style="font-size: 16px; font-weight: bold; text-align: center; border: 1px solid #ffffff;"
        colspan="{{$rowsCount}}">
        ТАБЕЛЬ
    </td>
</tr>
<tr style="border: 1px solid black;">
    <td style="font-size: 14px; font-weight: bold; text-align: center; border: 1px solid #ffffff;"
        colspan="{{$rowsCount}}">
        учета рабочего времени за {{AppService::monthes[$month]}} месяц {{$year}} г.
    </td>
</tr>
<tr style="border: 1px solid black;">
    <th style="border: 1px solid black; width: 64px; text-align: center; height: 128px;"></th>
    <th style="border: 1px solid black; width: 256px; text-align: center; height: 128px;"></th>
    <th style="border: 1px solid black; width: 256px; text-align: center; height: 128px;"></th>
    @foreach($days as $day)
        <th style="border: 1px solid black; width: 24px; font-size: 12px; text-align: center; height: 128px; font-weight: bold;">{{$day['dayTitle']}}</th>
    @endforeach
    <th style="border: 1px solid black; width: 64px; text-align: center; height: 128px; font-weight: bold;">Отраб.
        дней/смены
    </th>
    <th style="border: 1px solid black; width: 64px; text-align: center; height: 128px; font-weight: bold;">Отработано
        часов
    </th>
    <th style="border: 1px solid black; width: 64px; text-align: center; height: 128px; font-weight: bold;">Сверхуроч
        часы
    </th>
    <th style="border: 1px solid black; width: 24px; font-size: 12px; text-align: center; height: 128px; font-weight: bold;">
        Больничный
    </th>
    <th style="border: 1px solid black; width: 24px; font-size: 12px; text-align: center; height: 128px; font-weight: bold;">
        Труд отпуск
    </th>
    <th style="border: 1px solid black; width: 24px; font-size: 12px; text-align: center; height: 128px; font-weight: bold;">
        Отпуск б/с
    </th>
    <th style="border: 1px solid black; width: 48px; font-size: 12px; text-align: center; height: 128px; font-weight: bold;">
        Пропуск
    </th>
    <th style="border: 1px solid black; width: 24px; font-size: 12px; text-align: center; height: 128px; font-weight: bold;">
        Командировка
    </th>
    <th style="border: 1px solid black; width: 24px; font-size: 12px; text-align: center; height: 128px; font-weight: bold;">
        Праздничные
    </th>
</tr>
<tr style="border: 1px solid black;">
    <th style="border: 1px solid black; width: 64px; text-align: center; font-weight: bold;">№ п/п</th>
    <th style="border: 1px solid black; width: 256px; text-align: center; font-weight: bold;">Ф.И.О</th>
    <th style="border: 1px solid black; width: 256px; text-align: center; font-weight: bold;">Должность</th>
    @foreach($days as $day)
        <th style="border: 1px solid black; width: 24px; font-size: 12px; text-align: center;">{{$day['mNum']}}</th>
    @endforeach
    <th style="border: 1px solid black"></th>
    <th style="border: 1px solid black"></th>
    <th style="border: 1px solid black"></th>
    <th style="border: 1px solid black"></th>
    <th style="border: 1px solid black"></th>
    <th style="border: 1px solid black"></th>
    <th style="border: 1px solid black"></th>
    <th style="border: 1px solid black"></th>
    <th style="border: 1px solid black"></th>
</tr>
@foreach($users as $index => $user)
        <?php
        $planFact = AppService::getPlanFact($user, $month, null, $year, true);
        $workDays = $planFact['workDays'];
        $totals['factDays'] += $planFact['factDays'];
        $totals['fact'] += $planFact['fact'];
        $totals['overtime'] += $planFact['overtime'];
        $totals['sickLeave'] += $planFact['sickLeave'];
        $totals['laborLeave'] += $planFact['laborLeave'];
        $totals['freeVacation'] += $planFact['freeVacation'];
        $totals['wasnts'] += $planFact['wasnts'];
        $totals['businessTrip'] += $planFact['businessTrip'];
        $totals['celebrations'] += $planFact['celebrations'];
        ?>
    <tr style="border: 1px solid black;">
        <td style="border: 1px solid black; width: 64px; text-align: center;">{{$index+1}}</td>
        <td style="border: 1px solid black; width: 256px; text-align: center;">{{$user->surname . ' '. $user->firstname . ' '. $user->patronymic}}</td>
        <td style="border: 1px solid black; width: 256px; text-align: center;">{{$user->position->title}}</td>
        @foreach($days as $day)
            <td style="border: 1px solid black;
             width: 24px; font-size: 12px; text-align: center;
             color: {{AppService::getDayTypeOptimized($workDays, $day['yNum'], true, $companySettings)['text']}};
             background: {{AppService::getDayTypeOptimized($workDays, $day['yNum'], true, $companySettings)['background']}};
            ">
                {{AppService::formatHours(AppService::getDayTypeOptimized($workDays, $day['yNum'], false, $companySettings, $user))}}
            </td>
        @endforeach
        <td style="border: 1px solid black; font-weight: bold; text-align: center;">{{$planFact['factDays']}}</td>
        <td style="border: 1px solid black; font-weight: bold; text-align: center;">{{Arr::get(Arr::get($companySettings, 'roundSheetHours'), 'value') === true ? $planFact['roundFact'] :$planFact['fact']}}</td>
        <td style="border: 1px solid black; font-weight: bold; text-align: center;">{{$planFact['overtime']}}</td>
        <td style="border: 1px solid black; font-weight: bold; text-align: center;">{{$planFact['sickLeave']}}</td>
        <td style="border: 1px solid black; font-weight: bold; text-align: center;">{{$planFact['laborLeave']}}</td>
        <td style="border: 1px solid black; font-weight: bold; text-align: center;">{{$planFact['freeVacation']}}</td>
        <td style="border: 1px solid black; font-weight: bold; text-align: center;">{{$planFact['wasnts']}}</td>
        <td style="border: 1px solid black; font-weight: bold; text-align: center;">{{$planFact['businessTrip']}}</td>
        <td style="border: 1px solid black; font-weight: bold; text-align: center;">{{$planFact['celebrations']}}</td>
    </tr>
@endforeach

<tr style="border: 1px solid black;">
    <td style="border: 1px solid black; width: 64px; text-align: right; font-weight: bold;" colspan="{{$rowsCount-9}}">
        Итого:
    </td>
    <td style="border: 1px solid black; text-align: center; font-weight: bold;">{{$totals['factDays']}}</td>
    <td style="border: 1px solid black; text-align: center; font-weight: bold;">{{$totals['fact']}}</td>
    <td style="border: 1px solid black; text-align: center; font-weight: bold;">{{$totals['overtime']}}</td>
    <td style="border: 1px solid black; text-align: center; font-weight: bold;">{{$totals['sickLeave']}}</td>
    <td style="border: 1px solid black; text-align: center; font-weight: bold;">{{$totals['laborLeave']}}</td>
    <td style="border: 1px solid black; text-align: center; font-weight: bold;">{{$totals['freeVacation']}}</td>
    <td style="border: 1px solid black; text-align: center; font-weight: bold;">{{$totals['wasnts']}}</td>
    <td style="border: 1px solid black; text-align: center; font-weight: bold;">{{$totals['businessTrip']}}</td>
    <td style="border: 1px solid black; text-align: center; font-weight: bold;">{{$totals['celebrations']}}</td>
</tr>
<tr>
    <td></td>
    <td style="font-weight: bold;">Согласовано:</td>
</tr>
<tr></tr>
<tr>
    <td></td>
    <td>Руководитель отдела взыскания</td>
    <td style="font-size: 10px; font-style: italic; color:#717171;" colspan="8">
        (подпись)______________ (ФИО)______________________________
    </td>
</tr>
<tr>
    <td></td>
    <td></td>

    <td style="font-weight: bold; font-size: 14px;" colspan="8">Условные обозначения:</td>
    <td colspan="18"></td>
    <td colspan="18"></td>
</tr>
<tr></tr>
<tr>
    <td></td>
    <td></td>

    <td style="font-size: 14px;" colspan="8">Трудовой отпуск</td>
    <td style="font-weight: bold; font-size: 14px; text-align: center;">О</td>
    <td></td>
    <td></td>
    <td></td>
    <td style="font-size: 14px;" colspan="14">Пропуск</td>
    <td style="font-weight: bold; font-size: 14px; text-align: center;">Пр</td>
    <td></td>
    <td></td>
    <td></td>
    <td style="font-size: 14px;" colspan="8">Командировка выходного дня</td>
    <td style="font-weight: bold; font-size: 14px; text-align: center;">К</td>
</tr>
<tr>
    <td></td>
    <td></td>

    <td style="font-size: 14px;" colspan="8">Отпуск без сохранения заработной платы</td>
    <td style="font-weight: bold; font-size: 14px; text-align: center;">Б/С</td>
    <td></td>
    <td></td>
    <td></td>
    <td style="font-size: 14px;" colspan="14">Командировка</td>
    <td style="font-weight: bold; font-size: 14px; text-align: center;">К</td>
    <td></td>
    <td></td>
    <td></td>
    <td style="font-size: 14px;" colspan="8">Расторжение ТД</td>
    <td style="font-weight: bold; font-size: 14px; text-align: center;">У</td>
</tr>
<tr>
    <td></td>
    <td></td>
    <td style="font-size: 14px;" colspan="8">Нетрудоспособность (болезнь, карантин, и тд)</td>
    <td style="font-weight: bold; font-size: 14px; text-align: center;">Б</td>
    <td></td>
    <td></td>
    <td></td>
    <td style="font-size: 14px;" colspan="14">Отпуски в связи с родами (без сох з/п)</td>
    <td style="font-weight: bold; font-size: 14px; text-align: center;">А</td>
    <td></td>
    <td></td>
    <td></td>
    <td style="font-size: 14px;" colspan="8">Дополнительный отпуск</td>
    <td style="font-weight: bold; font-size: 14px; text-align: center;">Д/О</td>
</tr>
<tr>
    <td></td>
    <td></td>
    <td style="font-size: 14px;" colspan="8">Выходной день</td>
    <td style="font-weight: bold; font-size: 14px; text-align: center;">В</td>
    <td></td>
    <td></td>
    <td></td>
    <td style="font-size: 14px;" colspan="14">Праздник</td>
    <td style="font-weight: bold; font-size: 14px; text-align: center;">П</td>
    <td></td>
    <td></td>
    <td></td>
    <td style="font-size: 14px;" colspan="8"></td>
    <td style="font-weight: bold; font-size: 14px; text-align: center;"></td>
</tr>
