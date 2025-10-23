<script setup>
import { ref, computed } from "vue";
import * as XLSX from "xlsx";

const props = defineProps({
  users: {
    type: Array,
    required: true,
  },
  columns: {
    type: Array,
    required: true,
  }
});

const sortKey = ref("wpm");
const sortDir = ref("desc");

const sortedUsers = computed(() => {
    if (!sortKey.value) return props.users;
    return [...props.users].sort((a, b) => {
        let aVal, bVal;
        if (sortKey.value === "user") {
            aVal = a.user.name?.toLowerCase() || "";
            bVal = b.user.name?.toLowerCase() || "";
        } else {
            aVal = a[sortKey.value];
            bVal = b[sortKey.value];
        }
        if (aVal < bVal) return sortDir.value === "asc" ? -1 : 1;
        if (aVal > bVal) return sortDir.value === "asc" ? 1 : -1;
        return 0;
    });
});

function sortBy(key) {
  if (sortKey.value === key) {
    sortDir.value = sortDir.value === "asc" ? "desc" : "asc";
  } else {
    sortKey.value = key;
    sortDir.value = "desc";
  }
}
const downloadExcel = () => {
    // Step 1: Create header row from columns
  const headers = props.columns.map(col => col.label || col.key);
  // Step 2: Map data rows
  const data = props.users.map(user => 
    [user.user.name, ...props.columns.map(col => user[col])]
  );
  // Step 3: Combine headers + data
  const worksheetData = [props.columns, ...data];
  // Step 4: Create worksheet and workbook
  const worksheet = XLSX.utils.aoa_to_sheet(worksheetData);
  const workbook = XLSX.utils.book_new();
  XLSX.utils.book_append_sheet(workbook, worksheet, "Users");

  // Step 5: Save file
  XLSX.writeFile(workbook, "users.xlsx");
}
</script>

<template>
    <div class="w-full h-full p-0 m-0">
        <table class="w-full text-sm text-left text-gray-200 bg-black border border-gray-800">
            <thead class="bg-gray-900 text-gray-100">
                <tr>
                    <th class="px-4 py-3 font-bold">#</th>
                    <th class="px-4 py-3 font-bold cursor-pointer select-none"
                        @click="sortBy('user')"
                    >
                        User
                        <span v-if="sortKey === 'user'">
                            <svg v-if="sortDir === 'asc'" class="inline w-3 h-3 absolute" fill="currentColor" viewBox="0 0 20 20"><path d="M5.25 12.75L10 8l4.75 4.75" stroke="white" stroke-width="2" fill="none"/></svg>
                            <svg v-else class="inline w-3 h-3 absolute" fill="currentColor" viewBox="0 0 20 20"><path d="M5.25 7.25L10 12l4.75-4.75" stroke="white" stroke-width="2" fill="none"/></svg>
                        </span>
                    </th>
                    <th v-for="(col, idx) in columns" 
                        :key="idx" 
                        class="px-4 py-3 font-bold cursor-pointer select-none" 
                        @click="sortBy(col)"
                    >
                        {{ col }}
                        <span v-if="sortKey === col">
                            <svg v-if="sortDir === 'asc'" class="inline w-3 h-3 absolute" fill="currentColor" viewBox="0 0 20 20"><path d="M5.25 12.75L10 8l4.75 4.75" stroke="white" stroke-width="2" fill="none"/></svg>
                            <svg v-else class="inline w-3 h-3 absolute" fill="currentColor" viewBox="0 0 20 20"><path d="M5.25 7.25L10 12l4.75-4.75" stroke="white" stroke-width="2" fill="none"/></svg>
                        </span>
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(row, idx) in sortedUsers" :key="idx" class="border-b border-gray-800 hover:bg-gray-800">
                    <td class="px-4 py-2 font-bold">{{ idx + 1 }}</td>
                    <td class="px-4 py-2">{{ row.user.name }}</td>
                    <td v-for="(col, cIdx) in columns" :key="cIdx" class="px-4 py-2">
                        {{ row[col] }}
                    </td>
                </tr>
            </tbody>
        </table>
        <button
            @click="downloadExcel"
            class="bg-slate-900 hover:bg-slate-700 text-white font-semibold py-2 px-4 rounded-md shadow transition"
        >
            ⬇️ Download Excel
        </button>
    </div>
</template>

<style scoped>
.leaderboard {
    max-width: 600px;
    margin: auto;
}
</style>