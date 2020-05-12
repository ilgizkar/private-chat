<template>
    <div>
        <div class="card card-default chat-box">
        <div class="device-md visible-md"></div>
        <div class="card-header">
            <div class="row">
                <div class="col-md-1 avatar">
                    <div class="heading-avatar-icon">
                        <a data-fancybox="gallery" :href="src">
                            <img :src="src">
                        </a>
                    </div>
                </div>
                <div class="col-md-5 user-name">
                    <b :class="{'text-danger':session.block}">
                        {{ friend.name }}
                        <span style="font-weight:100; font-style: italic;" v-if="isTyping">  печатает...</span>
                        <span v-if="session.block"> (Заблокирован)</span>
                    </b>
                </div>
                <div class="col-md-6 tools">
                    <a href="" title="Закрыть диалоговое окно" @click.prevent="close">
                        <i class="fa fa-times mt3 float-right" aria-hidden="true"></i>
                    </a>

                    <div class="dropdown float-right mr-4">
                        <a href="" title="Возможные действия" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item" href="#" @click.prevent="unblock" v-if="session.block && can">Разблокировать</a>
                            <a class="dropdown-item" href="#" @click.prevent="block" v-if="!session.block">Заблокировать</a>
                            <a class="dropdown-item" href="#" @click.prevent="clear">Очистить диалог</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body" v-chat-scroll>
            <div class="card-text" v-for="chat in chats" :key="chat.id">
                <only-message :message=chat></only-message>
            </div>
        </div>
        <form class="card-footer flex" @submit.prevent="send">
            <div class="form-group" style="width: 100%">
                <input type="text" class="form-control" placeholder="Введите текст сообщения"
                :disabled="session.block == 1" v-model="message">
            </div>
            <div class="ml15">
                <i class="fa fa-send fa-2x" @click.prevent="send" title="Отправить" aria-hidden="true"></i>
            </div>
        </form>
    </div>

    </div>
</template>

<script>
    import OnlyMessage from "./OnlyMessage";
    export default {
        components: {OnlyMessage},
        props: ['friend'],
        data() {
            return {
                chats: [],
                message: null,
                isTyping: false,
                manAva: 'https://bootdey.com/img/Content/avatar/avatar6.png',
                womanAva: 'https://www.bootdey.com/img/Content/avatar/avatar5.png',
                avatar: '',
                gender: ''
            }
        },
        computed: {
            session() {
                return this.friend.session;
            },
            can() {
                return this.session.blocked_id == auth.id;
            },
            src () {
              return this.avatar
            }
        },
        watch: {
            message(value) {
                if(value) {
                    Echo.private(`Chat.${this.friend.session.id}`).whisper('typing', {
                            name: auth.name
                        })
                }
            }
        },
        methods: {
            send() {
                if(this.message) {
                    this.pushToChats(this.message);
                    axios.post(`/send/${this.friend.session.id}`, {
                        contents: this.message,
                        to_user: this.friend.id
                    }).then(res => this.chats[this.chats.length - 1].id = res.data);
                    this.message = null
                }
            },
            pushToChats(message){
                this.chats.push({message: message, type: 0, read_at: null, sent_at: 'Только что'});
            },
            close() {
                this.$emit('close')
            },
            clear() {
                axios.post(`/session/${this.friend.session.id}/clear`).then(res => this.chats = [])
            },
            block() {
                this.session.block = true;
                axios
                    .post(`/session/${this.friend.session.id}/block`)
                    .then(res => this.session.blocked_id = auth.id)
            },
            unblock() {
                this.session.block = false;
                axios
                    .post(`/session/${this.friend.session.id}/unblock`)
                    .then(res => this.session.blocked_id = null)
            },
            getAllMessages() {
                axios
                    .post(`/session/${this.friend.session.id}/chats`)
                    .then(res => (this.chats = res.data.data));
            },
            read() {
                axios
                    .post(`/session/${this.friend.session.id}/read`);
            }
        },
        created() {
            this.read();

            axios.post(`/session/${this.friend.session.id}/user`).then(res => {
                this.gender = res.data.data.gender;
                this.avatar = res.data.data.avatar ? '/storage/' + res.data.data.avatar : '';

                if(!this.avatar) {
                    if(this.gender === 0) {
                        this.avatar = this.manAva;
                    } else {
                        this.avatar = this.womanAva;
                    }
                }
            });

            this.getAllMessages();

            Echo.private(`Chat.${this.friend.session.id}`).listen('PrivateChatEvent', e => {
                this.friend.session.open ? this.read() : '';
                this.chats.push({message:e.content, type: 1,sent_at: 'Только что'})
            });

            Echo.private(`Chat.${this.friend.session.id}`).listen('MsgReadEvent', e =>
                this.chats.forEach(
                    chat => (chat.id === e.chat.id ? chat.read_at = e.chat.read_at : '')
                )
            );

            Echo.private(`Chat.${this.friend.session.id}`).listen('BlockEvent', e =>
                this.session.block = e.blocked
            );

            Echo.private(`Chat.${this.friend.session.id}`).listenForWhisper('typing', e => {
                this.isTyping = true
                setTimeout(() => {
                    this.isTyping = false
                }, 2000)
            });
        }
    }
</script>

<style scoped>
    .chat-box {
        height: 500px;
    }
    .card-body {
        overflow-y:scroll;
        display: grid;
        background-image: url('https://static.tumblr.com/c7019329e77254042f874bede15c1222/txzha1z/OxNnokise/tumblr_static_tumblr_static_4gtieatoh3eo8ccwggco0k0cc_focused_v3.jpg');
    }
    .mt3 {
        margin-top: 3px;
    }
    .ml15 {
        margin-left: 15px;
        color: #308052;
        margin-top: 4px;
    }
    .dropdown-menu {
        left: -55px !important;
    }
    .flex {
        display: flex;
        padding-bottom: 0;
    }

    @media screen and (max-device-width: 480px) {
        .user-name {
            width: 55%;
        }
        .avatar {
            width: 22%;
        }
        .tools {
            width: 23%;
        }
    }
    img {
        height: 40px;
        width: 40px;
        border-radius: 50%;
    }
    .user-name, .tools {
        padding: 10px 0 0 0;
    }
    .user-name b {
        color: #919191;
    }
    .tools {
        margin-left: -20px;
    }
    .tools i {
        color: #919191;
    }
</style>
