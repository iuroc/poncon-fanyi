<template>
    <nav class="navbar navbar-expand navbar-light bg-light border-bottom shadow-sm">
        <div class="container">
            <a class="navbar-brand ms-2" href="">鹏创翻译</a>
            <button
                class="navbar-toggler"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#navbarNavAltMarkup"
                aria-controls="navbarNavAltMarkup"
                aria-expanded="false"
                aria-label="Toggle navigation"
            >
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
                <div class="navbar-nav">
                    <a class="nav-link active" href="https://ouyangpeng.top"
                        >官网</a
                    >
                    <a class="nav-link" href="#">Github</a>
                    <a class="nav-link" href="#">关于</a>
                </div>
            </div>
        </div>
    </nav>
    <div class="container py-3 py-sm-4">
        <div class="row">
            <div
                class="col-lg-6"
                v-for="(item, index) in items"
                v-bind:key="index"
            >
                <div class="form-floating mb-3 mb-lg-4">
                    <textarea
                        class="form-control"
                        v-model="item.value"
                        placeholder="待翻译内容"
                        style="height: 200px"
                    ></textarea>
                    <label>{{ item.text }}</label>
                </div>
                <div v-if="!index" class="row mb-3 mb-lg-0 mx-0">
                    <div class="col-6 pe-2 pe-sm-3 ps-0">
                        <button class="btn w-100 btn-success text-nowrap me-3">
                            选择语言
                        </button>
                    </div>
                    <div class="col-3 pe-2 pe-sm-3 ps-0">
                        <button class="btn w-100 text-nowrap btn-danger">
                            清空
                        </button>
                    </div>
                    <div class="col-3 px-0">
                        <button class="btn w-100 text-nowrap btn-primary">
                            翻译
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "App",
    data() {
        return {
            items: [
                {
                    text: "待翻译内容",
                    value: "",
                },
                {
                    text: "翻译结果",
                    value: "",
                },
            ],
        };
    },
    components: {},
};
</script>

<style>
</style>