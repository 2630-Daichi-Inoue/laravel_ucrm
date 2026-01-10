<script setup>
import FlashMessage from '@/Components/FlashMessage.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import Pagination from '@/Components/Pagination.vue';
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';

defineProps({
    customers: Object,
})

const search = ref('')

const searchCustomers = () => {
    router.get(route('customers.index', { search: search.value }))
}

</script>

<template>
    <Head title="商品一覧" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800"
            >
                顧客一覧
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">

                        <section class="text-gray-600 body-font">
                            <div class="container px-5 py-8 mx-auto">
                                <FlashMessage />
                                <div class="flex pl-4 my-4 lg:w-2/3 w-full mx-auto">
                                    <div>
                                        <input type="text" name="search" v-model="search">
                                        <button @click="searchCustomers" class="bg-blue-300 text-white py-2 px-2">検索</button>
                                    </div>
                                    <Link as="button" :href="route('customers.create')" class="flex ml-auto text-white bg-indigo-500 border-0 py-2 px-6 focus:outline-none hover:bg-indigo-600 rounded">顧客登録</Link>
                                </div>
                                <div class="lg:w-2/3 w-full mx-auto overflow-auto">
                                    <table class="table-auto w-full text-left whitespace-no-wrap">
                                        <thead>
                                        <tr>
                                            <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 rounded-tl rounded-bl">ID</th>
                                            <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">氏名</th>
                                            <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">メアド</th>
                                            <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">電話番号</th>
                                            <th class="w-10 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 rounded-tr rounded-br"></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr v-for="customer in customers.data" :key="customer.id">
                                            <td class="px-4 py-3 border-b-2 border-gray-200">
                                                {{ customer.id }}
                                            </td>
                                            <td class="px-4 py-3 border-b-2 border-gray-200">{{ customer.name }}</td>
                                            <td class="px-4 py-3 border-b-2 border-gray-200">{{ customer.email }}</td>
                                            <td class="px-4 py-3 border-b-2 border-gray-200">{{ customer.tel }}</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>

                            </div>
                            <Pagination class="mt-6" :links="customers.links"></Pagination>
                        </section>

                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
