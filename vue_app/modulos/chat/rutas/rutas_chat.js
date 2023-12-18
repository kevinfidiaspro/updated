import Chat from '../componentes/Chat'
import ListaChats from '../componentes/ListaChats'

const routes = [
    ...route(`/chat`, Chat, {
        Auth: true,
    }),
    ...route(`/lista-chats`, ListaChats, {
        Auth: true,
        req_admin: true
    }),
]

function route(path, component = Default, meta = {}) {
    return [{
        path,
        component,
        meta
    }]
}

export default routes