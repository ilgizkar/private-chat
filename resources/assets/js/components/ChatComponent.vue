<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-3">
                <div class="card card-default">
                    <div class="card-header">Приватный чат</div>
                        <ul class="list-group">
                            <li class="list-group-item">Назиф</li>
                            <li class="list-group-item">Гузель</li>
                        </ul>
                </div>
            </div>
            <div class="col-9">
                <message-component
                    v-if="open"
                    @close="close"
                ></message-component>
            </div>
        </div>
    </div>
</template>

<script>
    import MessageComponent from "./MessageComponent";
    export default {
        data() {
            return {
                open: true
            }
        },
        methods: {
            close() {
                this.open = false
            }
        },
        mounted() {
            console.log('Component mounted.')
        },
        components: {
            MessageComponent
        }
    }
</script>
