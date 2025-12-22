<template>
    <MilkdownProvider>
        <ProsemirrorAdapterProvider>
            <div style="position: relative;" class="fbfm_md_editor">
                <div ref="crepe_editor_vue" class="crepe-editor feed_md_content fbfm_md_seditor_wrapper"></div>
            </div>
        </ProsemirrorAdapterProvider>
    </MilkdownProvider>
    <div class="fbfm_mention_pop_wrap" v-if="!disable_mention">
        <el-popover
            popper-class="fbfm_mention_pop"
            ref="mentionPopoverRef"
            placement="bottom"
            :visible="showMentionPop"
            :width="300"
            :persistent="false">
            <template #reference>
                <span style=""></span>
            </template>
            <div v-if="!justReplaced">
                <ul v-if="mentionResults.length">
                    <li v-for="member in mentionResults" :key="member.user_id"
                        @click="addMention('@' + member.username)">
                        <img v-if="member.avatar" alt="" :src="member.avatar"/>
                        <span>{{ member.display_name }} (@{{ member.username }})</span>
                    </li>
                </ul>
                <div v-else-if="!searchingMention">
                    <p>
                        No results found. Please search by username
                    </p>
                </div>
            </div>
        </el-popover>
    </div>
</template>

<script setup>
import { ref, watch, onMounted, onBeforeUnmount } from 'vue'
import { Crepe } from './Crepe/src/core/crepe'
import { CrepeFeature } from './Crepe/src/feature'
import { replaceAll } from '@milkdown/kit/utils'
import { editorViewCtx } from '@milkdown/kit/core'
import { MilkdownProvider } from '@milkdown/vue'
import { ProsemirrorAdapterProvider } from '@prosemirror-adapter/vue'
import { TextSelection } from 'prosemirror-state'
import Rest from '@/Bits/Rest.js'

const props = defineProps({
        modelValue: {
            type: String,
            default: ''
        },
        placeholder: {
            type: String,
            default: ''
        },
        autofocus: {
            type: Boolean,
            default: false
        },
        mentionQuery: {
            type: Object,
            default: () => ({})
        },
        autofocus_changed: {
            type: Boolean,
            default: false
        },
        disable_mention: {
            type: Boolean,
            default: false
        },
        disable_media: {
            type: Boolean,
            default: false
        },
        defaultMentions: {
            type: Array,
            default: () => []
        }
})

const emit = defineEmits(['update:modelValue', 'handleMediaUpload', 'editorCreated'])

const crepe_editor_vue = ref(null)
const mentionPopoverRef = ref(null)
const editor = ref(null)
const currentContent = ref('')
const showMentionPop = ref(false)
const currentMentionSearch = ref('')
const mentionResults = ref([])
const justReplaced = ref(false)
const searchingMention = ref(false)

watch(() => props.autofocus_changed, (value) => {
            if (value) {
        focusOnEditor()
            }
})

watch(() => props.modelValue, (newValue) => {
    if (newValue !== currentContent.value && editor.value) {
        currentContent.value = newValue
    }
})

const updateContent = (emoji) => {
    if (!editor.value) return
    
    editor.value.action((ctx) => {
        const view = ctx.get(editorViewCtx)
        const {state} = view
        const {from} = state.selection

        const insertText = emoji

        const transaction = state.tr.insertText(insertText, from)
        const newCursorPosition = from + insertText.length

                const newTransaction = transaction.setSelection(
                    TextSelection.create(transaction.doc, newCursorPosition)
        )

        view.dispatch(newTransaction)
        view.focus()
    })
}

const initEditor = () => {
    if (!crepe_editor_vue.value) return
    
            const crepe = new Crepe({
        root: crepe_editor_vue.value,
        defaultValue: props.modelValue,
        features: {
            [CrepeFeature.BlockEdit]: false, // Disable block edit (slash menu) - only keep toolbar popover
            [CrepeFeature.LinkTooltip]: false // Disable link tooltip - we use toolbar for links
        },
                featureConfigs: {
            [CrepeFeature.Placeholder]: {
                text: props.placeholder || 'Type your message here!',
                        mode: 'doc'
                    }
                },
                onUpdate(markdown) {
            emit('update:modelValue', markdown)
            currentContent.value = markdown
                },
                async handleCommonActions(actionName, ctx) {
                    if (actionName === 'editor_updated') {
                if (props.disable_mention) {
                    return false
                        }
                checkforMention(ctx)
            } else if (actionName === 'handleFileUpload') {
                if (props.disable_media) {
                    return []
                        }
                return await handleFileUpload(ctx)
                    }
                }
    })
    
            crepe.create()
                .then(() => {
            focusOnEditor()
            emit('editorCreated')
            editor.value = crepe.getEditor()
        })
}

const focusOnEditor = () => {
    if (!props.autofocus) {
        return
            }

    const el = crepe_editor_vue.value
    if (el && el.querySelector('.ProseMirror')) {
        let p = el.querySelector('.ProseMirror')
        p.focus()
        if (p.hasChildNodes()) {
            let s = window.getSelection()
            let r = document.createRange()
            let e = p.childElementCount > 0 ? p.lastChild : p
            r.setStart(e, 1)
            r.setEnd(e, 1)
            s.removeAllRanges()
            s.addRange(r)
                }
            }
}

const checkforMention = (ctx) => {
    let message = currentContent.value

            // check if the message has any @ symbol
    if (!message.includes('@') || justReplaced.value) {
        showMentionPop.value = false
        currentMentionSearch.value = ''
        return
            }

    const view = ctx.get(editorViewCtx)
    const {state} = view
    const {selection} = state
            if (!selection) {
        return
            }
            // get the last word starts with @ symbol in the current block
    const currentBlockText = state.doc.textBetween(0, selection.to, ' ')

    const lastMetion = currentBlockText.match(/@([\w-_]+)$/)
            if (!lastMetion) {
        showMentionPop.value = false
        currentMentionSearch.value = ''
        return
            }

    let mention = lastMetion[1]

            if (!mention || mention.length < 3) {
        if (props.defaultMentions && props.defaultMentions.length) {
            let mentionLower = mention.toLowerCase()
            let filteredMention = props.defaultMentions.filter((item) => {
                return item.username.toLowerCase().includes(mentionLower) || item.display_name.toLowerCase().includes(mentionLower)
            })

                    if (filteredMention.length) {
                currentMentionSearch.value = mention
                mentionResults.value = filteredMention
                showMentionPop.value = true
                    }
                }
        return
            }

    if (currentMentionSearch.value) {
        currentMentionSearch.value = mention
        if (searchingMention.value) {
            return false
                }
            }

    searchingMention.value = true
    currentMentionSearch.value = mention
    
    Rest.get('members', {mention: currentMentionSearch.value, ...props.mentionQuery})
                .then(response => {
            mentionResults.value = response.members.data
            showMentionPop.value = true
                })
                .finally(() => {
            searchingMention.value = false
        })
}

const addMention = (mention) => {
    if (props.disable_mention) {
        return
            }

    currentContent.value = currentContent.value.replace('@' + currentMentionSearch.value, mention + '&nbsp;')
    if (editor.value) {
        editor.value.action(replaceAll(currentContent.value))
    }
    focusOnEditor()
    showMentionPop.value = false
    currentMentionSearch.value = ''
    justReplaced.value = true
            setTimeout(() => {
        justReplaced.value = false
    }, 2000)
}

const handleFileUpload = async (config) => {
    const files = config.files
            for (let i = 0; i < files.length; i++) {
        const file = files.item(i)
                if (!file) {
            continue
                }

                // You can handle whatever the file type you want, we handle image here.
                if (!file.type.includes('image')) {
            continue
                }

        emit('handleMediaUpload', file)
            }

    return []
        }

onMounted(() => {
    setTimeout(() => {
        initEditor()
    }, 100)
})

onBeforeUnmount(() => {
    if (editor.value) {
        editor.value.destroy()
        }
})
</script>

<style scoped>
.fbfm_md_editor {
    position: relative;
}

.fbfm_md_seditor_wrapper {
    min-height: 68px;
}
</style>
