<script setup>
    import { Link } from '@inertiajs/vue3';
</script>
<template>
    Inertiaテストです。<br>
    <a href="/">aタグ経由です。</a><br>
    <Link href="/">Linkコンポーネント経由です。</Link><br>
    <Link :href="route('inertia.index')">名前付きルートの確認です。</Link><br>
    <Link :href="route('inertia.show', { id: 1 })">パラメータのテストです。</Link>
</template>
