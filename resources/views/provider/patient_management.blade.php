<table id="apiTable" class="w-full text-sm text-left display">

    <thead class="bg-[#F7F9FB] font-normal text-[#00000080]">
        <tr>
            <th class="px-1 py-4 font-normal w-[160px]">Patient Name</th>
            <th class="px-1 py-4 font-normal w-[120px]">Unique ID</th>
            <th class="px-1 py-4 font-normal w-[140px]">DOB</th>
            <th class="px-1 py-4 font-normal w-[80px]">Age</th>
            <th class="px-1 py-4 font-normal w-[100px]">Gender</th>
            <th class="px-1 py-4 font-normal w-[160px]">Patient Results</th>
        </tr>
    </thead>
    <tbody class="text-sm text-[#000000]">

        <tr class="border-b border-[#000000]/10 mb-3">
            <td class="px-1 py-4 w-[160px]">William John</td>
            <td class="px-1 py-4 w-[120px]">YTEJCMFG</td>
            <td class="px-1 py-4 w-[140px]">24 July, 2025</td>
            <td class="px-1 py-4 w-[80px]">40</td>
            <td class="px-1 py-4 w-[100px]">Male</td>
            <td class="px-1 py-4 w-[160px]">
                <select name="patient_result" class="flex items-center gap-2 text-[#2889AA] font-semibold bg-transparent px-2"
                    onchange="if(this.value) window.location.href=this.value">
                    <option disabled selected>View More</option>
                    <option value="dexcom">Dexcom/Libre</option>
                    <option value="passio_ai">Passio AI</option>
                    <option value="/provider/clinical-notes">Clinical notes</option>
                    <option value="/provider/quest-lab">Quest Lab</option>
                    <option value="/provider/e-prescription">E-Prescriptions</option>
                </select>
            </td>
        </tr>

    </tbody>
</table>
