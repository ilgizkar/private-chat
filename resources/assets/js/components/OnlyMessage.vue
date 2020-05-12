<template>
    <p :class="{'sender': message.type == 0, 'to-user': message.type == 1}">
        {{ message.message }}
        <br>
        <span style="font-size: 8px;" v-if="message.read_at">{{ message.read_at }}</span>
        <span style="font-size: 8px;" v-else>1 second ago</span>
        <a href="#" v-if="message.type == 1 && !isCyrillic" @click.prevent="getApi"><i class="fa fa-globe float-right" title="Перевести" style="margin-top: 7px;" aria-hidden="true"></i></a>
        <i class="fa fa-check float-right" title="Прочитанно" v-if="message.type == 0 && message.read_at != null" style="margin-top: 7px;" aria-hidden="true"></i>
    </p>
</template>

<script>
    export default {
        props: ['message'],
        computed: {
            isCyrillic() {
                return /[а-я]/i.test(this.message.message);
            }
        },
        methods: {
            getApi(){
                axios.post('https://translate.yandex.net/api/v1.5/tr.json/translate?key=trnsl.1.1.20200512T071123Z.0a6c82afec51bbeb.a9c13582c16c3b192a600cbce956360bdb357b98&text='+ this.message.message +'&lang=en-ru')
                    .then(res => {
                        this.message.message = res.data.text[0]
                    })
            }
        }
    }
</script>

<style scoped>
    .sender {
        background: #dcf8c6;
        min-width: 200px;
        max-width: 500px;
        float: right;
        padding: 4px 10px 7px !important;
        text-shadow: 0 0px 1px rgba(0, 0, 0, .2);
        border-radius: 10px 10px 0 10px;
        min-height: 50px;
        box-shadow: 0 0 12px rgb(171, 165, 165);
        color: #308052;
    }
    .to-user {
        background: white;
        width: max-content;
        min-width: 200px;
        max-width: 500px;
        text-shadow: 0 0px 1px rgba(0, 0, 0, .2);
        padding: 4px 10px 7px !important;
        border-radius: 10px 10px 10px 0;
        min-height: 50px;
        box-shadow: 0 0 12px rgb(171, 165, 165);
        color: #908585;
    }

    @media screen and (max-device-width: 480px) {
        .to-user {
            max-width: 200px;
        }

        .sender {
            max-width: 200px;
        }
    }
</style>
