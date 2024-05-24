<div
    x-data="noticesHandler()"
    class="fixed inset-1  w-auto top-0 right-0 left-auto bottom-auto inset-0 flex flex-col-reverse items-end justify-start"
    @toast.window="add($event.detail)"
    style="pointer-events:none">
    <template x-for="notice of notices" :key="notice.id">
        <div
            x-show="visible.includes(notice)"
            x-transition:enter="transition ease-in duration-200"
            x-transition:enter-start="transform opacity-0 translate-y-2"
            x-transition:enter-end="transform opacity-100"
            x-transition:leave="transition ease-out duration-500"
            x-transition:leave-start="transform translate-x-0 opacity-100"
            x-transition:leave-end="transform translate-x-full opacity-0"
            @click="remove(notice.id)"
            class="toast"
            :class="[notice.type]"
            style="pointer-events:all"
            x-html="showBox(notice)"
        >
        </div>
    </template>

    <script>
        function noticesHandler() {
            return {
                notices: [],
                visible: [],
                add(notice) {
                    notice = notice[0];
                    notice.id = Date.now()
                    this.notices.push(notice)
                    this.fire(notice.id)
                },
                fire(id) {
                    this.visible.push(this.notices.find(notice => notice.id == id))
                    const timeShown = 2000 * this.visible.length
                    setTimeout(() => {
                        this.remove(id)
                    }, timeShown)
                },
                remove(id) {
                    const notice = this.visible.find(notice => notice.id == id)
                    const index = this.visible.indexOf(notice)
                    this.visible.splice(index, 1)
                },
                showBox(notice) {
                    switch (notice.type) {
                        case "success":
                            html = '<span class="icon"><svg viewBox="0 0 24 24" class="mcon" fill="currentColor" xmlns="http://www.w3.org/2000/svg"> <use xlink:href="/assets/images/icons.svg#success"></use></svg></span>'
                                + '<span class="text">' + notice.message + '</span>';
                            break;
                        case "info":
                            html = '<span class="icon"><svg viewBox="0 0 24 24" class="mcon" fill="currentColor" xmlns="http://www.w3.org/2000/svg"> <use xlink:href="/assets/images/icons.svg#info"></use></svg></span>'
                                + '<span class="text">' + notice.message + '</span>';
                            break;
                        case "warning":
                            html = '<span class="icon"><svg viewBox="0 0 24 24" class="mcon" fill="currentColor" xmlns="http://www.w3.org/2000/svg"> <use xlink:href="/assets/images/icons.svg#warning"></use></svg></span>'
                                + '<span class="text">' + notice.message + '</span>';
                            break;
                        case "error":
                            html = '<span class="icon"><svg viewBox="0 0 24 24" class="mcon" fill="currentColor" xmlns="http://www.w3.org/2000/svg"> <use xlink:href="/assets/images/icons.svg#error"></use></svg></span>'
                                + '<span class="text">' + notice.message + '</span>';
                            break;
                        default:
                            html = '<span class="icon"><svg viewBox="0 0 24 24" class="mcon" fill="currentColor" xmlns="http://www.w3.org/2000/svg"> <use xlink:href="/assets/images/icons.svg#campaign"></use></svg></span>'
                                + '<span class="text">' + notice.message + '</span>';
                    }
                    return html;
                }
            }
        }

    </script>
</div>
