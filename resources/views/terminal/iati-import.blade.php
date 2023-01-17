<div class="mx-2 my-1">
    <div class="space-x-1">
        <span class="px-1 bg-green-500 text-white">IATI codelists Import</span>
    </div>

    <div class="mt-1">
        <span class="font-bold text-green">Result</span>

        <div class="flex space-x-1">
            <span class="font-bold">Total Codelists</span>
            <span class="flex-1 content-repeat-[.] text-gray"></span>
            <span class="font-bold text-green">{{$total_lists}}</span>
        </div>

        <div class="flex space-x-1">
            <span class="font-bold">Imported Codelists</span>
            <span class="flex-1 content-repeat-[.] text-gray"></span>
            <span class="font-bold text-green">{{$imported}}</span>
        </div>

        <div class="flex space-x-1">
            <span class="font-bold">Imported Languages</span>
            <span class="flex-1 content-repeat-[.] text-gray"></span>
            <span class="font-bold text-green">{{implode(",", $translations)}}</span>
        </div>
    </div>
</div>