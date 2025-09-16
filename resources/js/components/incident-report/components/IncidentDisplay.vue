<script lang="ts" setup>
import { Avatar, AvatarFallback } from '@/components/ui/avatar'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import {
  Select,
  SelectContent,
  SelectGroup,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select'
import { Separator } from '@/components/ui/separator'
import { useEditorSetup } from '@/composables/useEditorSetup'
import { useIncidentForm } from '@/composables/useIncidentForm'
import { useUserPermissions } from '@/composables/useUserPermissions'
import type { IncidentDisplayProps, Incident } from '@/types/incident'
import { Icon } from '@iconify/vue'
import { EditorContent } from '@tiptap/vue-3'
import { format } from 'date-fns'
import { computed, onMounted, watch } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps<IncidentDisplayProps>()
const emit = defineEmits(['cancel-edit', 'no-incident', 'incident-updated'])
const { canVerify } = useUserPermissions()

const {
  formData,
  infractionTypes,
  showOtherInfractionInput,
  loadIncidentData,  
  clearForm,        
} = useIncidentForm()

const {
  editor,
  showColorDropdown,
  showFontSizeDropdown,
  textColors,
  fontSizes,
} = useEditorSetup(formData.value.description, (html) => {
  formData.value.description = html
})

const isEditing = computed(() => props.incident?.status === 'draft')

onMounted(() => {
  if (props.incident) {
    loadIncidentData(props.incident)
  }
})

watch(() => props.incident, (newIncident) => {
  if (newIncident) {
    loadIncidentData(newIncident)
  } else {
    clearForm()
  }
}, { immediate: true })

const handleApiError = (error: any, context: string) => {
  console.error(`${context} failed:`, error)
  alert(`Failed to ${context}. Please try again.`)
}

const submitIncident = async () => {
  try {
    const validEmployees = formData.value.involvedEmployees
      .filter((e) => e.id && e.name)
      .map((e) => e.id)

    if (validEmployees.length !== formData.value.involvedEmployees.length) {
      throw new Error('Some employee selections were invalid')
    }

    const url = isEditing.value 
      ? route('incidents.update', { incident: props.incident?.id })
      : route('incidents.store')

    const method = isEditing.value ? 'put' : 'post'

    await router[method](url, {
      job_order_id: formData.value.jobOrder,
      subject: formData.value.subject,
      location: formData.value.location,
      infraction_type: formData.value.infractionType,
      occured_at: formData.value.dateTime,
      description: formData.value.description,
      involved_employees: validEmployees,
    }, {
      onSuccess: () => {
        emit('incident-updated')
        emit('cancel-edit')
      },
      onError: (errors) => {
        handleApiError(errors, 'submit the report')
      }
    })
  } catch (error: any) {
    handleApiError(error, 'submit the report')
  }
}

const markNoIncident = async () => {
  try {
    await router.put(route('incidents.markNoIncident', { 
      incident: props.incident.id  
    }), {}, {
      onSuccess: () => {
        const updatedIncident: Incident = {
          ...props.incident,
          status: 'no_incident',
          subject: 'No Incident Reported',
          completed_at: new Date().toISOString()
        }
        
        emit('no-incident', updatedIncident)
      },
      onError: () => {
        handleApiError(null, 'mark as no incident')
      }
    })
  } catch (error) {
    handleApiError(error, 'mark as no incident')
  }
}

const verifyIncident = async (id: string) => {
  try {
    await router.patch(route('incidents.verify', { incident: id }), {}, {
      onSuccess: () => {
        emit('incident-updated')
        alert('Incident verified successfully!')
      },
      onError: () => {
        handleApiError(null, 'verify the incident')
      }
    })
  } catch (error) {
    handleApiError(error, 'verify the incident')
  }
}

const getJobOrderDisplay = (incident: any) => {
  if (!incident) return 'N/A';
  
  if (incident.hauling_job_order) {
    return `JO-${incident.hauling_job_order.id}`;
  }
  
  if (incident.job_order) {
    return `JO-${incident.job_order.id}`;
  }
  
  if (incident.job_order_id) {
    return `JO-${incident.job_order_id}`;
  }
  
  return 'N/A';
}

const getFormDataJobOrderDisplay = () => {
  if (!formData.value.jobOrder) return 'N/A';
  return `JO-${formData.value.jobOrder}`;
}
</script>

<template>
  <div class="flex h-full flex-col overflow-auto">
    <div v-if="!incident" class="flex flex-1 flex-col items-center justify-center p-8 text-center">
      <div class="mb-4 rounded-full bg-gray-100 p-4">
        <Icon icon="lucide:file-text" class="size-8 text-gray-400" />
      </div>
      <h3 class="mb-2 text-lg font-medium">No Incident Report Selected</h3>
      <p class="text-muted-foreground max-w-md">
        Select an incident report from the list to view details or create a new report.
      </p>
    </div>

    <div
      v-else-if="isEditing"
      class="flex flex-1 flex-col"
    >
      <div class="flex items-center justify-between p-4 border-b">
        <h2 class="text-lg font-semibold">
          Edit Incident Report
        </h2>
        <Button
          variant="outline"
          @click="markNoIncident"
          :disabled="!formData.jobOrder"
        >
          <Icon icon="lucide:check-circle" class="mr-2 size-4" />
          No Incident Today
        </Button>
      </div>
      <Separator />

      <div class="space-y-6 p-5">
        <div class="grid grid-cols-2 gap-4">
          <div class="flex items-center gap-4">
            <Label class="whitespace-nowrap">People Involved:</Label>
            <div class="flex-1">
              <div class="w-full border-b pb-2">
                {{ formData.involvedEmployees.map(e => e.name).join(', ') || 'No personnel involved' }}
              </div>
            </div>
          </div>

          <div class="flex items-center gap-4">
            <Label class="whitespace-nowrap">Date and Time:</Label>
            <div class="flex-1">
              <div class="w-full border-b pb-2">
                {{ formData.dateTime ? format(new Date(formData.dateTime), 'PPpp') : 'N/A' }}
              </div>
            </div>
          </div>

          <div class="flex items-center gap-4">
            <Label class="whitespace-nowrap">Location:</Label>
            <div class="flex-1">
              <Input
                v-model="formData.location"
                class="w-full rounded-none border-0 border-b pb-2 shadow-none focus-visible:ring-0"
              />
            </div>
          </div>

          <div class="flex items-center gap-4">
            <Label class="whitespace-nowrap">Type of Infraction:</Label>
            <div class="flex-1">
              <Select
                v-model="formData.infractionType"
                @update:modelValue="showOtherInfractionInput = $event === 'Others'"
              >
                <SelectTrigger class="w-full rounded-none border-0 border-b pb-2 shadow-none focus:ring-0 focus-visible:ring-0">
                  <SelectValue :placeholder="formData.infractionType || 'Select type'" />
                </SelectTrigger>
                <SelectContent>
                  <SelectGroup>
                    <SelectItem
                      v-for="type in infractionTypes"
                      :key="type"
                      :value="type"
                    >
                      {{ type }}
                    </SelectItem>
                  </SelectGroup>
                </SelectContent>
              </Select>
              <Input
                v-if="showOtherInfractionInput"
                v-model="formData.infractionType"
                placeholder="Please specify"
                class="mt-2 w-full rounded-none border-0 border-b pb-2 shadow-none focus-visible:ring-0"
              />
            </div>
          </div>

          <div class="flex items-center gap-4">
            <Label class="whitespace-nowrap">Job Order:</Label>
            <div class="flex-1">
              <div class="w-full border-b pb-2">
                {{ getFormDataJobOrderDisplay() }}
              </div>
            </div>
          </div>
        </div>
        
        <div class="flex items-center gap-4">
          <Label class="whitespace-nowrap">Subject:</Label>
          <div class="flex-1">
            <Input
              v-model="formData.subject"
              class="w-full border-0 shadow-none focus-visible:ring-0"
            />
          </div>
        </div>
      </div>
      
      <div>
        <div class="flex items-start">
          <div class="flex-1 space-y-2">
            <div
              v-if="editor"
              class="border bg-white"
            >
              <div class="flex flex-wrap items-center gap-1 bg-gray-50 p-2">
                <Button
                  size="sm"
                  variant="ghost"
                  @click="editor.chain().focus().undo().run()"
                >
                  <Icon
                    icon="lucide:undo"
                    class="size-4"
                  />
                </Button>
                <Button
                  size="sm"
                  variant="ghost"
                  @click="editor.chain().focus().redo().run()"
                >
                  <Icon
                    icon="lucide:redo"
                    class="size-4"
                  />
                </Button>

                <Button
                  size="sm"
                  variant="ghost"
                  :class="{ 'bg-gray-200': editor.isActive('bold') }"
                  @click="editor.chain().focus().toggleBold().run()"
                >
                  <Icon
                    icon="lucide:bold"
                    class="size-4"
                  />
                </Button>
                <Button
                  size="sm"
                  variant="ghost"
                  :class="{ 'bg-gray-200': editor.isActive('italic') }"
                  @click="editor.chain().focus().toggleItalic().run()"
                >
                  <Icon
                    icon="lucide:italic"
                    class="size-4"
                  />
                </Button>
                <Button
                  size="sm"
                  variant="ghost"
                  :class="{ 'bg-gray-200': editor.isActive('underline') }"
                  @click="editor.chain().focus().toggleUnderline().run()"
                >
                  <Icon
                    icon="lucide:underline"
                    class="size-4"
                  />
                </Button>
                <Button
                  size="sm"
                  variant="ghost"
                  :class="{ 'bg-gray-200': editor.isActive('strike') }"
                  @click="editor.chain().focus().toggleStrike().run()"
                >
                  <Icon
                    icon="lucide:strikethrough"
                    class="size-4"
                  />
                </Button>

                <Button
                  size="sm"
                  variant="ghost"
                  :class="{ 'bg-gray-200': editor.isActive('bulletList') }"
                  @click="editor.chain().focus().toggleBulletList().run()"
                >
                  <Icon
                    icon="lucide:list"
                    class="size-4"
                  />
                </Button>
                <Button
                  size="sm"
                  variant="ghost"
                  :class="{ 'bg-gray-200': editor.isActive('orderedList') }"
                  @click="editor.chain().focus().toggleOrderedList().run()"
                >
                  <Icon
                    icon="lucide:list-ordered"
                    class="size-4"
                  />
                </Button>

                <Button
                  size="sm"
                  variant="ghost"
                  :class="{ 'bg-gray-200': editor.isActive('blockquote') }"
                  @click="editor.chain().focus().toggleBlockquote().run()"
                >
                  <Icon
                    icon="lucide:quote"
                    class="size-4"
                  />
                </Button>
                <Button
                  size="sm"
                  variant="ghost"
                  :class="{ 'bg-gray-200': editor.isActive('codeBlock') }"
                  @click="editor.chain().focus().toggleCodeBlock().run()"
                >
                  <Icon
                    icon="lucide:code"
                    class="size-4"
                  />
                </Button>
                <div class="relative">
                  <Button
                    variant="ghost"
                    size="sm"
                    @click="showColorDropdown = !showColorDropdown"
                  >
                    <Icon
                      icon="lucide:paint-bucket"
                      class="size-4"
                    />
                  </Button>
                  <div
                    v-if="showColorDropdown"
                    class="absolute z-50 mt-2 grid w-36 grid-cols-4 gap-1 rounded border bg-white p-2 shadow"
                  >
                    <div
                      v-for="color in textColors"
                      :key="color"
                      :style="{ backgroundColor: color }"
                      class="h-6 w-6 cursor-pointer rounded border border-gray-200 hover:border-black"
                      @click="
                        () => {
                          editor?.chain().focus().setColor(color).run()
                          showColorDropdown = false
                        }
                      "
                    ></div>
                  </div>
                </div>
                <div class="relative">
                  <Button
                    variant="ghost"
                    size="sm"
                    @click="showFontSizeDropdown = !showFontSizeDropdown"
                  >
                    <Icon
                      icon="lucide:text"
                      class="size-4"
                    />
                  </Button>
                  <div
                    v-if="showFontSizeDropdown"
                    class="absolute z-50 mt-2 w-28 rounded border bg-white p-2 shadow"
                  >
                    <div
                      v-for="size in fontSizes"
                      :key="size"
                      class="cursor-pointer px-2 py-1 text-sm hover:bg-gray-100"
                      @click="
                        () => {
                          editor?.chain().focus().setFontSize(size).run()
                          showFontSizeDropdown = false
                        }
                      "
                    >
                      {{ size }}px
                    </div>
                  </div>
                </div>
              </div>

              <EditorContent
                :editor="editor"
                class="prose min-h-[300px] max-w-[1200px] border-none p-3 outline-none"
              />
            </div>
          </div>
        </div>
      </div>

      <div class="flex justify-end gap-2 p-4">
        <Button
          variant="outline"
          @click="emit('cancel-edit')"
        >
          Cancel Edit
        </Button>
        <Button @click="submitIncident">
          Update Report
        </Button>
      </div>
    </div>
    
    <div
      v-else
      class="flex flex-1 flex-col p-4"
    >
      <div class="flex items-start p-5">
        <div class="text-xl font-bold">{{ incident.subject }}</div>
      </div>

      <Separator />
      <div class="flex items-start p-4">
        <div class="flex items-start gap-4 text-sm">
          <Avatar>
            <AvatarFallback>
              {{
                incident?.created_by?.name
                  ?.split(' ')
                  .map((n) => n[0])
                  .join('') || '?'
              }}
            </AvatarFallback>
          </Avatar>
          <div class="grid gap-1">
            <div class="font-semibold">
              <div class="font-semibold">
                {{ incident?.created_by?.name ?? 'Unknown' }}
              </div>
            </div>
            <div class="line-clamp-1 text-xs">
              {{ incident.created_by?.email ?? 'Unknown@gmail.com' }}
            </div>
            <div class="text-xs text-muted-foreground">
              Published:
              {{
                incident?.occured_at
                  ? format(new Date(incident.occured_at), 'PPpp')
                  : 'N/A'
              }}
            </div>
            <div class="text-xs text-muted-foreground">
              Status: {{ incident.status }}
            </div>
          </div>
        </div>
      </div>
      
      <div class="space-y-4 p-5">
        <div
          v-if="incident.haulers?.length || incident.involved_employees?.length"
          class="mt-4"
        >
          <h3 class="font-medium">
            {{ incident.haulers?.length ? 'Haulers' : 'Involved Employees' }}
          </h3>
          <div class="mt-2 flex flex-wrap gap-2">
            <Badge
              v-for="person in (incident.haulers?.length ? incident.haulers : incident.involved_employees)"
              :key="person.id"
              class="flex items-center gap-1"
            >
              <span>{{ person.name }}</span>
              <span
                v-if="person.email"
                class="text-xs opacity-75"
              >({{ person.email }})</span>
            </Badge>
          </div>
        </div>
        <div
          v-else
          class="mt-4"
        >
          <h3 class="font-medium">Involved Personnel</h3>
          <p class="text-sm text-muted-foreground">No personnel involved</p>
        </div>
        
        <div>
          <h3 class="font-medium">Date and Time of Incident</h3>
          <p>
            {{
              incident?.occured_at
                ? format(new Date(incident.occured_at), 'PPpp')
                : 'N/A'
            }}
          </p>
        </div>

        <div>
          <h3 class="font-medium">Location</h3>
          <p>{{ incident.location }}</p>
        </div>

        <div>
          <h3 class="font-medium">Type of Infraction</h3>
          <p>{{ incident.infraction_type }}</p>
        </div>

        <div>
          <h3 class="font-medium">Job Order</h3>
          <p>{{ getJobOrderDisplay(incident) }}</p>
        </div>

        <div>
          <h3 class="font-medium">Status</h3>
          <Badge
            :variant="incident.status === 'resolved' ? 'default' : 'secondary'"
          >
            {{ incident.status }}
          </Badge>
        </div>
      </div>

      <div
        class="prose max-w-none p-4"
        v-html="incident.description"
      ></div>
      
      <div
        v-if="canVerify && incident.status === 'for verification'"
        class="flex justify-end gap-2 p-4"
      >
        <Button
          variant="default"
          @click="verifyIncident(incident.id)"
        >
          Verify Incident
        </Button>
      </div>
    </div>
  </div>
</template>