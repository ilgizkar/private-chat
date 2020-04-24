<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-3">
                <div class="card card-default">
                    <div class="card-header">Приватный чат</div>
                        <ul class="list-group">
                            <li class="list-group-item" v-for="friend in friends" :key="friend.id">
                                <a href="" @click.prevent="openChat(friend)">
                                    {{ friend.name }}
                                    <span class="text-danger" v-if="friend.session && (friend.session.unreadCount > 0)">+{{ friend.session.unreadCount }}</span>
                                </a>
                                <i class="fa fa-circle float-right text-success" aria-hidden="true" v-if="friend.online"></i>
                            </li>
                        </ul>
                </div>
            </div>
            <div class="col-9">
                <span v-for="friend in friends" :key="friend.id" v-if="friend.session">
                     <message-component
                         v-if="friend.session.open"
                         @close="close(friend)"
                         :friend=friend
                     ></message-component>
                </span>
            </div>
        </div>
    </div>
</template>

<script>
    import MessageComponent from "./MessageComponent";
    export default {
        data() {
            return {
                friends: []
            }
        },
        methods: {
            close(friend) {
                friend.session.open = false
            },
            audioNotyPlay() {
                var audio = new Audio(); // Создаём новый элемент Audio
                audio.src = 'http://soundbible.com/mp3/Elevator Ding-SoundBible.com-685385892.mp3'; // Указываем путь к звуку "клика"
                audio.autoplay = true; // Автоматически запускаем
            },
            getFriends() {
                axios.post('/getFriends').then(res => {
                    this.friends = res.data.data;
                    this.friends.forEach(friend => {
                        if(friend.session) {
                            this.listenForEverySession(friend)
                        }
                    })
                })
            },
            listenForEverySession(friend) {
                Echo.private(`Chat.${friend.session.id}`).listen('PrivateChatEvent', e => {
                    if(friend.session.open) {
                        return ''
                    } else {
                        friend.session.unreadCount++;
                        this.audioNotyPlay();
                    }
                });
            },
            openChat(friend) {
                if(friend.session) {
                   this.closeAll();
                    friend.session.open = true;
                    friend.session.unreadCount = 0;
                } else {
                    this.createSession(friend);
                }
            },
            createSession(friend) {
                this.closeAll();
                axios.post('/session/create', {friend_id:friend.id})
                    .then(res => {(friend.session = res.data.data), (friend.session.open = true)});
            },
            closeAll() {
                this.friends.forEach(friend => {
                    if(friend.session) {
                        friend.session.open = false
                    }
                });
            }
        },
        created() {
            this.getFriends();
            Echo.channel('Chat').listen('SessionEvent', e => {
               let friend = this.friends.find(friend => friend.id == e.session_by);
               friend.session = e.session;
               this.listenForEverySession(friend);
            });
            Echo.join('Chat')
                .here((users) => {
                    this.friends.forEach(friend => {
                        users.forEach(user => {
                            if(user.id == friend.id){
                                friend.online = true
                            }
                         })
                     })
                })
                .joining((user) => {
                    this.friends.forEach(friend => {
                        friend.id == user.id ? friend.online = true : ''
                    })
                })
                .leaving((user) => {
                    this.friends.forEach(friend => {
                        friend.id == user.id ? friend.online = false :''
                    })
                });
        },
        components: {
            MessageComponent
        }
    }
</script>
