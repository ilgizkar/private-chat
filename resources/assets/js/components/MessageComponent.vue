<template>
    <div class="card card-default chat-box">
        <div class="card-header">
            <b :class="{'text-danger':session_block}">
                Гузель
                <span v-if="session_block"> (Заблокирован)</span>
            </b>

            <a href="" @click.prevent="close">
                <i class="fa fa-times float-right" aria-hidden="true"></i>
            </a>

            <div class="dropdown float-right mr-4">
                <a href="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                </a>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    <a class="dropdown-item" href="#" @click.prevent="block" v-if="!session_block">Заблокировать</a>
                    <a class="dropdown-item" href="#" @click.prevent="unblock" v-else>Разблокировать</a>
                    <a class="dropdown-item" href="#" @click.prevent="clear">Очистить диалог</a>
                </div>
            </div>
        </div>
        <div class="card-body" v-chat-scroll>
            <p class="card-text" v-for="chat in chats" :key="chat.message">
                {{ chat.message }}
            </p>
        </div>
        <form class="card-footer" @submit.prevent="send">
            <div class="form-group">
                <input type="text" class="form-control" placeholder="Введите текст сообщения"
                :disabled="session_block">
            </div>
        </form>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                chats: [],
                session_block: false
            }
        },
        methods: {
            send() {
                console.log('wwww')
            },
            close() {
                this.$emit('close')
            },
            clear() {
                this.chats = []
            },
            block() {
                this.session_block = true
            },
            unblock() {
                this.session_block = false
            }
        },
        created() {
            this.chats.push(
                {message: 'Привет'},
                {message: 'Как дела?'},
                {message: 'Что делаешь?'},
                )
        }
    }
</script>

<style scoped>
    .chat-box {
        height: 400px;
    }
    .card-body {
        overflow-y:scroll;
    }
</style>
