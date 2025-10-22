<tr></tr>
<tr>
    <td></td>
    <td>Статистика рабочего времени сотрудников</td>
</tr>
<tr>
    <td></td>
    <td>Наименование компании:&nbsp; <strong>{{$company->title}}</strong></td>
</tr>
<tr>
    <td></td>
    <td>Дата: c &nbsp;<strong>{{\Carbon\Carbon::parse($data['from'])->format('d.m.Y')}}</strong>&nbsp;до&nbsp;<strong>{{\Carbon\Carbon::parse($data['to'])->format('d.m.Y')}}</strong></td>
</tr>
<tr></tr>
<tr>
    <td></td>
    <th style="font-weight: bold; text-align: center; width: 48px; border: 1px solid black; background-color: #f2f2f2;">№</th>
    <th style="font-weight: bold; text-align: center; width: 400px; border: 1px solid black; background-color: #f2f2f2;">ФИО</th>
    <th style="font-weight: bold; text-align: center; width: 128px; border: 1px solid black; background-color: #f2f2f2;">Филиал</th>
    <th style="font-weight: bold; text-align: center; width: 128px; border: 1px solid black; background-color: #f2f2f2;">Отдел</th>
    <th style="font-weight: bold; text-align: center; width: 96px; border: 1px solid black; background-color: #f2f2f2;">Посещения</th>
    <th style="font-weight: bold; text-align: center; width: 96px; border: 1px solid black; background-color: #f2f2f2;">Опоздания</th>
    <th style="font-weight: bold; text-align: center; width: 96px; border: 1px solid black; background-color: #f2f2f2;">Пропуски</th>
    <th style="font-weight: bold; text-align: center; width: 128px; border: 1px solid black; background-color: #f2f2f2;">План рабочих часов</th>
    <th style="font-weight: bold; text-align: center; width: 128px; border: 1px solid black; background-color: #f2f2f2;">Факт рабочих часов</th>
    <th style="font-weight: bold; text-align: center; width: 128px; border: 1px solid black; background-color: #f2f2f2;">Разница</th>
</tr>
@foreach($stats['users'] as $index => $user)
    <tr>
        <td></td>
        <td style="text-align: center; width: 48px; border: 1px solid black;">{{$index+1}}</td>
        <td style="text-align: center; width: 400px; border: 1px solid black;">{{$user['fullName']}}</td>
        <td style="text-align: center; width: 128px; border: 1px solid black;">{{$user['branch']}}</td>
        <td style="text-align: center; width: 128px; border: 1px solid black;">{{$user['departmentName']}}</td>
        <td style="text-align: center; width: 96px; border: 1px solid black;">{{$user['wasCount'] ?: '0'}}/{{$user['workCount']?:'0'}}</td>
        <td style="text-align: center; width: 96px; border: 1px solid black;">{{$user['lateCount'] ?: '0' }}/{{$user['workCount']?:'0'}}</td>
        <td style="text-align: center; width: 128px; border: 1px solid black;">{{$user['wasntCount'] ?: '0'}}/{{$user['workCount']?:'0'}}</td>
        <td style="text-align: center; width: 128px; border: 1px solid black;">{{$user['plan']}}</td>
        <td style="text-align: center; width: 128px; border: 1px solid black;">{{$user['hourCount']}}</td>
        <td style="text-align: center; width: 128px; border: 1px solid black;">{{$user['difHours'] ?: '-'}}</td>
    </tr>
@endforeach
<tr></tr>
<tr>
    <td></td>
</tr>
