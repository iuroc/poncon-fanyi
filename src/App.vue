<template>
    <nav
        class="
            navbar navbar-expand navbar-light
            bg-light
            border-bottom
            shadow-sm
        "
    >
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
                    <a
                        class="nav-link active"
                        href="https://ouyangpeng.top"
                        target="_blank"
                        >官网</a
                    >
                    <a
                        class="nav-link"
                        href="https://github.com/oyps/poncon-fanyi"
                        target="_blank"
                        >Github</a
                    >
                    <a class="nav-link" href="https://ouyangpeng.top/#about"
                        >关于</a
                    >
                </div>
            </div>
        </div>
    </nav>
    <div class="container py-3 py-sm-4">
        <div class="row">
            <div class="col-lg-6">
                <div class="form-floating mb-3 mb-lg-4">
                    <textarea
                        class="form-control shadow-sm border border-3"
                        v-model="input"
                        placeholder="待翻译内容"
                        style="height: 200px"
                    ></textarea>
                    <label>待翻译内容（限5000字）</label>
                </div>
                <div class="row mb-3 mb-lg-0 mx-0">
                    <div class="col-6 pe-2 pe-sm-3 ps-0">
                        <button
                            class="btn w-100 btn-success text-nowrap me-3"
                            @click="changeLang"
                        >
                            {{ getLangModeText(langModeIndex) }}
                        </button>
                    </div>
                    <div class="col-4 pe-2 pe-sm-3 ps-0">
                        <button
                            class="btn w-100 text-nowrap btn-primary"
                            @click="translate"
                            :disabled="loading"
                        >
                            <span
                                class="spinner-border spinner-border-sm"
                                role="status"
                                aria-hidden="true"
                                v-if="loading"
                            ></span>
                            翻译
                        </button>
                    </div>
                    <div class="col-2 px-0">
                        <button
                            class="btn w-100 text-nowrap btn-danger"
                            @click="clear"
                        >
                            <i class="bi bi-trash3"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div
                    class="
                        p-3
                        shadow-sm
                        rounded
                        border border-3
                        result
                        overflow-auto
                        mb-3 mb-lg-4
                    "
                    style="height: 200px"
                    :style="{ 'text-indent': result.length > 1 ? '2em' : '0' }"
                >
                    <template v-for="(line, index) in result" :key="index">
                        <div class="line" v-if="line[0].tgt">
                            <span
                                class="sentence"
                                v-for="(sentence, index2) in line"
                                :key="index2"
                            >
                                {{ sentence.tgt }}
                            </span>
                        </div>
                    </template>
                </div>
                <div class="row mx-0">
                    <div class="col pe-2 pe-sm-3 ps-0">
                        <button
                            class="btn btn-secondary w-100 copybtn"
                            data-clipboard-target=".result"
                        >
                            复制结果
                        </button>
                    </div>
                    <div class="col ps-0 pe-0">
                        <button class="btn btn-danger w-100">朗读结果</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div
        class="modal fade modal-chooseLanguage user-select-none"
        id="staticBackdrop"
        tabindex="-1"
        aria-labelledby="staticBackdropLabel"
        aria-hidden="true"
    >
        <div
            class="
                modal-dialog modal-fullscreen-sm-down modal-dialog-scrollable
            "
        >
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">
                        选择语言
                    </h5>
                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"
                    ></button>
                </div>
                <div class="modal-body">
                    <div class="list-group">
                        <template
                            v-for="(item, index) in langChoose"
                            :key="index"
                        >
                            <div
                                class="list-group-item list-group-item-action"
                                :class="{ active: index == langModeIndex }"
                                :data-index="index"
                                @click="clickChangeLang(index)"
                            >
                                {{ getLangModeText(index) }}
                            </div>
                        </template>
                    </div>
                </div>
                <div class="modal-footer">
                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal"
                    >
                        关闭
                    </button>
                    <button type="button" class="btn btn-primary">确定</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";
import { Modal } from "bootstrap";
export default {
    name: "App",
    data() {
        return {
            input: "",
            result: [],
            // items: [
            //     {
            //         text: "待翻译内容（限5000字）",
            //         value: "",
            //     },
            //     {
            //         text: "翻译结果",
            //         value: "",
            //     },
            // ],
            from: "AUTO",
            to: "AUTO",
            loading: false,
            errorCode: {
                10: "抱歉，个别句子太长啦，我读不懂",
                20: "抱歉，超过5000字实在太长啦，让我喘口气",
                30: "抱歉，我绞尽脑汁了",
                40: "抱歉，我还在学习该语种中",
                50: "抱歉，请不要频繁请求服务",
                transRequestError: "翻译出错，请检查网络后重试",
            },
            langModeIndex: 0,
            language: {
                "zh-CHS": "中文",
                en: "英语",
                ko: "韩语",
                ja: "日语",
                fr: "法语",
                ru: "俄语",
                es: "西班牙语",
                pt: "葡萄牙语",
                hi: "印地语",
                ar: "阿拉伯语",
                da: "丹麦语",
                de: "德语",
                el: "希腊语",
                fi: "芬兰语",
                it: "意大利语",
                ms: "马来语",
                vi: "越南语",
                id: "印尼语",
                nl: "荷兰语",
                th: "泰语",
            },
            langChoose: [
                ["AUTO"],
                ["zh-CHS", "en"],
                ["en", "zh-CHS"],
                ["zh-CHS", "ja"],
                ["ja", "zh-CHS"],
                ["zh-CHS", "ko"],
                ["ko", "zh-CHS"],
                ["zh-CHS", "fr"],
                ["fr", "zh-CHS"],
                ["zh-CHS", "de"],
                ["de", "zh-CHS"],
                ["zh-CHS", "ru"],
                ["ru", "zh-CHS"],
                ["zh-CHS", "es"],
                ["es", "zh-CHS"],
                ["zh-CHS", "pt"],
                ["pt", "zh-CHS"],
                ["zh-CHS", "it"],
                ["it", "zh-CHS"],
                ["zh-CHS", "vi"],
                ["vi", "zh-CHS"],
                ["zh-CHS", "id"],
                ["id", "zh-CHS"],
                ["zh-CHS", "ar"],
                ["ar", "zh-CHS"],
                ["zh-CHS", "nl"],
                ["nl", "zh-CHS"],
                ["zh-CHS", "th"],
                ["th", "zh-CHS"],
            ],
        };
    },
    methods: {
        /**
         * 翻译
         */
        translate() {
            if (this.input.length > 5000) {
                alert("请不要超过5000字");
                return;
            }
            if (this.input.match(/^\s*$/) || this.loading) {
                return;
            }
            this.loading = true;
            this.$axios
                .post(
                    "http://localhost:3000/api/fanyi.php",
                    {
                        from: this.from,
                        to: this.to,
                        text: this.input,
                    },
                    {
                        timeout: 10000,
                    }
                )
                .then((response) => {
                    this.loading = false;
                    var data = response.data;
                    if (data.errorCode != 0) {
                        alert(this.errorCode[data.errorCode]);
                        return;
                    }
                    this.result = data.translateResult;
                    var type = data.type.split("2");
                    this.langChoose.forEach((item, index) => {
                        if (item[0] == type[0] && item[1] == type[1]) {
                            this.langModeIndex = index;
                        }
                    });
                })
                .catch((error) => {
                    this.loading = false;
                    alert("请求超时，请重试");
                });
        },
        /**
         * 清空
         */
        clear() {
            if ((this.input || this.result) && confirm("确定清空吗？")) {
                this.input = "";
                this.result = [];
            }
        },
        /**
         * 切换语言
         * 展示模态框
         */
        changeLang() {
            this.myModal.show();
        },
        getLangModeText(index) {
            var data = this.langChoose[index];
            return data.length == 1
                ? "自动检测语言"
                : `${this.language[data[0]]} » ${this.language[data[1]]}`;
        },
        clickChangeLang(index) {
            this.myModal.hide();
            this.langModeIndex = index;
            this.from = this.langChoose[index][0];
            this.to = this.langChoose[index][1];
        },
        // 支持朗读
        // zh-CHS2en: "eng"
        // zh-CHS2fr: "fr"
        // zh-CHS2ja: "jap"
        // zh-CHS2ko: "ko"
    },
    components: {},
    computed: {
        _isMobile() {
            let flag = navigator.userAgent.match(
                /(phone|pad|pod|iPhone|iPod|ios|iPad|Android|Mobile|BlackBerry|IEMobile|MQQBrowser|JUC|Fennec|wOSBrowser|BrowserNG|WebOS|Symbian|Windows Phone)/i
            );
            return flag;
        },
    },
    mounted() {
        document.body.ondragstart = () => {
            return false;
        };
        this.myModal = new Modal(
            document.querySelector(".modal-chooseLanguage")
        );
        window.addEventListener("keyup", (event) => {
            if (event.keyCode == 83 && event.ctrlKey) {
                event.preventDefault();
                this.translate();
            }
        });
        window.addEventListener("keydown", (event) => {
            if (event.keyCode == 83 && event.ctrlKey) {
                event.preventDefault();
            }
        });
    },
};
</script>

<style>
.row textarea {
    transition: all 0.3s;
}

.row textarea:focus {
    border-color: var(--bs-primary) !important;
}
.row .result {
    cursor: text;
    transition: all 0.3s;
}
.row .result .line:not(:last-child) {
    margin-bottom: 0.5rem;
}

.result .line {
    /* line-height: 20px; */
    word-wrap: break-word !important;
    word-break: break-all !important;
    /* letter-spacing: 1px; */
}

.result .line .sentence {
    transition: all 0.2s;
    /* padding-bottom: 5px; */
}

*::selection {
    background: rgba(88, 86, 213, 0.28) !important;
    color: inherit;
}
*::-moz-selection {
    background: rgba(88, 86, 213, 0.28) !important;
    color: inherit;
}

*::-webkit-selection {
    background: rgba(88, 86, 213, 0.28) !important;
    color: inherit;
}

@media (min-width: 992px) {
    .row textarea,
    .row .result {
        height: 250px !important;
    }
}
</style>