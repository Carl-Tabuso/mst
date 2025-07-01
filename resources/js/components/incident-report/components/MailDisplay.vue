<script lang="ts" setup>
import { format } from 'date-fns'
import { Icon } from '@iconify/vue'
import {computed} from 'vue'
import { Avatar, AvatarFallback } from '@/components/ui/avatar'
import { Button } from '@/components/ui/button'
import {Badge} from '@/components/ui/badge'
import { Label } from '@/components/ui/label'
import { useEditor, EditorContent } from '@tiptap/vue-3'
import { Separator } from '@/components/ui/separator'
import { Input } from '@/components/ui/input'
import { useUserPermissions } from '@/composables/useUserPermissions';
import { Combobox, ComboboxAnchor, ComboboxEmpty, ComboboxGroup, ComboboxInput, ComboboxItem, ComboboxList } from '@/components/ui/combobox'
import { TagsInput, TagsInputInput, TagsInputItem, TagsInputItemDelete, TagsInputItemText } from '@/components/ui/tags-input'
import {
  Select,
  SelectContent,
  SelectGroup,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select'
import axios from 'axios'
import { useEditorSetup } from '@/composables/useEditorSetup'
import { useIncidentForm } from '@/composables/useIncidentForm'
import type { MailDisplayProps } from '@/types/incident'

const props = defineProps<MailDisplayProps>()
const emit = defineEmits(['cancel-compose'])
const { canVerify, canCompose } = useUserPermissions();

const {
  formData,
  employees,
  jobOrders,
  infractionTypes,
  showOtherInfractionInput,
  searchTerm,
  filteredEmployees,
  employeeNames,
  selectedEmployeeIds,
  onJobOrderSelected,
  handleEmployeeSelect,
  addCurrentSearchTerm,
  handleTagsUpdate
} = useIncidentForm()

const { 
  editor, 
  showColorDropdown, 
  showFontSizeDropdown, 
  textColors, 
  fontSizes 
} = useEditorSetup(
  formData.value.description,
  (html) => { formData.value.description = html }
)

const mailFallbackName = computed(() => {
  return props.mail?.created_by.name
    .split(' ')
    .map(chunk => chunk[0])
    .join('')
})

const submitIncident = async () => {
  try {
    const validEmployees = formData.value.involvedEmployees
      .filter(e => e.id && e.name)
      .map(e => e.id)
    
    if (validEmployees.length !== formData.value.involvedEmployees.length) {
      throw new Error('Some employee selections were invalid')
    }

    await axios.post('/incidents', {
      job_order_id: formData.value.jobOrder,
      subject: formData.value.subject,
      location: formData.value.location,
      infraction_type: formData.value.infractionType,
      occured_at: formData.value.dateTime, 
      description: formData.value.description,
      involved_employees: validEmployees,
    })

    
    alert('Incident submitted successfully!')
    emit('cancel-compose')
    emit('incident-submitted')
  } catch (error: any) {
    console.error('Submission failed:', error)
    alert(`Failed to submit the report: ${error.response?.data?.message || error.message}`)
  }
}
const verifyIncident = async (id: string) => {
  try {
    await axios.patch(`/incidents/${id}/verify`);
    // Refresh the incident data or emit an event to parent
    emit('incident-submitted');
    alert('Incident verified successfully!');
  } catch (error) {
    console.error('Verification failed:', error);
    alert('Failed to verify the incident');
  }
};
</script>
<template>
  <div class="flex h-full flex-col">
    <div v-if="isComposing" class="flex flex-1 flex-col ">
  
      
      <Separator />
      
      <div class="p-5 space-y-6">
        <!-- 2x2 Grid Section -->
        <div class="grid grid-cols-2 gap-4">
          <!-- People Involved -->
          <div class="flex items-center gap-4">
            <Label class="whitespace-nowrap">People Involved:</Label>
            <div class="flex-1">
<Combobox v-model="selectedEmployeeIds" multiple>
  <ComboboxAnchor as-child>
    <TagsInput 
      v-model="employeeNames" 
      class="gap-2 w-full border-0"
      @update:modelValue="handleTagsUpdate"
    >
      <div class="flex gap-2 flex-wrap items-center">
        <TagsInputItem 
          v-for="(name, index) in employeeNames" 
          :key="index" 
          :value="name"
        >
          <TagsInputItemText>
            {{ name }}
          </TagsInputItemText>
          <TagsInputItemDelete />
        </TagsInputItem>
      </div>
      <ComboboxInput 
        v-model="searchTerm" 
        as-child
        @keydown.enter.prevent="addCurrentSearchTerm"
      >
        <TagsInputInput 
          placeholder="Search for an Employee" 
          class="min-w-[100px] w-full pb-2 border-0 border-b rounded-none shadow-none focus-visible:ring-0 h-auto" 
        />
      </ComboboxInput>
    </TagsInput>
  </ComboboxAnchor>
  
  <ComboboxList class="w-[--reka-popper-anchor-width]">
    <ComboboxEmpty>No employees found</ComboboxEmpty>
    <ComboboxGroup>
      <ComboboxItem
        v-for="employee in filteredEmployees" 
        :key="employee.id" 
        :value="employee.id"
        @select="handleEmployeeSelect(employee.id)"
      >
        {{ employee.name }}
      </ComboboxItem>
    </ComboboxGroup>
  </ComboboxList>
</Combobox>
            </div>
          </div>

          <div class="flex items-center gap-4">
            <Label class="whitespace-nowrap">Date and Time:</Label>
            <div class="flex-1">
              <Input 
                type="datetime-local" 
                v-model="formData.dateTime" 
                class="w-full pb-2 border-0 border-b rounded-none shadow-none focus-visible:ring-0" 
              />
            </div>
          </div>

          <!-- Location of Infraction -->
          <div class="flex items-center gap-4">
            <Label class="whitespace-nowrap">Location:</Label>
            <div class="flex-1">
              <Input 
                v-model="formData.location" 
                class="w-full pb-2 border-0 border-b rounded-none shadow-none focus-visible:ring-0" 
              />
            </div>
          </div>

          <!-- Type of Infraction -->
          <div class="flex items-center gap-4">
            <Label class="whitespace-nowrap">Type of Infraction:</Label>
            <div class="flex-1">
              <Select v-model="formData.infractionType" @update:modelValue="showOtherInfractionInput = $event === 'Others'">
                <SelectTrigger class="w-full pb-2 border-0 border-b rounded-none shadow-none focus:ring-0 focus-visible:ring-0">
                  <SelectValue placeholder="Select type" />
                </SelectTrigger>
                <SelectContent>
                  <SelectGroup>
                    <SelectItem v-for="type in infractionTypes" :key="type" :value="type">
                      {{ type }}
                    </SelectItem>
                  </SelectGroup>
                </SelectContent>
              </Select>
              <Input 
                v-if="showOtherInfractionInput"
                v-model="formData.infractionType"
                placeholder="Please specify"
                class="w-full pb-2 border-0 border-b rounded-none shadow-none focus-visible:ring-0 mt-2"
              />
            </div>
          </div>
       

        <!-- Type of Service -->
        <div class="flex items-center gap-4">
          <Label class="whitespace-nowrap">Type of Service:</Label>
          <div class="flex-1">
            <Input 
              v-model="formData.serviceType" 
              readonly
              class="w-full pb-2 border-0 border-b rounded-none shadow-none focus-visible:ring-0" 
            />
          </div>
        </div>
           <div class="flex items-center gap-4">
            <Label class="whitespace-nowrap">Job Order:</Label>
            <div class="flex-1">
<Select v-model="formData.jobOrder" @update:modelValue="onJobOrderSelected" class="border-none border-b rounded-none">
  <SelectTrigger class="...">
    <SelectValue placeholder="Select a job order" />
  </SelectTrigger>
  <SelectContent>
    <SelectGroup>
      <SelectItem
        v-for="job in jobOrders"
        :key="job.id"
        :value="job.id"
      >
        {{ job.label }}
      </SelectItem>
    </SelectGroup>
  </SelectContent>
</Select>

            
            </div>
          </div>
        
 </div>
        <!-- Subject (no border) -->
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
    <div v-if="editor" class="border  bg-white">
      <div class="flex flex-wrap items-center gap-1 p-2  bg-gray-50">
        <Button size="sm" variant="ghost" @click="editor.chain().focus().undo().run()">
          <Icon icon="lucide:undo" class="size-4" />
        </Button>
        <Button size="sm" variant="ghost" @click="editor.chain().focus().redo().run()">
          <Icon icon="lucide:redo" class="size-4" />
        </Button>

        <Button size="sm" variant="ghost" :class="{ 'bg-gray-200': editor.isActive('bold') }"
          @click="editor.chain().focus().toggleBold().run()">
          <Icon icon="lucide:bold" class="size-4" />
        </Button>
        <Button size="sm" variant="ghost" :class="{ 'bg-gray-200': editor.isActive('italic') }"
          @click="editor.chain().focus().toggleItalic().run()">
          <Icon icon="lucide:italic" class="size-4" />
        </Button>
        <Button size="sm" variant="ghost" :class="{ 'bg-gray-200': editor.isActive('underline') }"
          @click="editor.chain().focus().toggleUnderline().run()">
          <Icon icon="lucide:underline" class="size-4" />
        </Button>
        <Button size="sm" variant="ghost" :class="{ 'bg-gray-200': editor.isActive('strike') }"
          @click="editor.chain().focus().toggleStrike().run()">
          <Icon icon="lucide:strikethrough" class="size-4" />
        </Button>

        <Button size="sm" variant="ghost" :class="{ 'bg-gray-200': editor.isActive('bulletList') }"
          @click="editor.chain().focus().toggleBulletList().run()">
          <Icon icon="lucide:list" class="size-4" />
        </Button>
        <Button size="sm" variant="ghost" :class="{ 'bg-gray-200': editor.isActive('orderedList') }"
          @click="editor.chain().focus().toggleOrderedList().run()">
          <Icon icon="lucide:list-ordered" class="size-4" />
        </Button>

        <Button size="sm" variant="ghost" :class="{ 'bg-gray-200': editor.isActive('blockquote') }"
          @click="editor.chain().focus().toggleBlockquote().run()">
          <Icon icon="lucide:quote" class="size-4" />
        </Button>
        <Button size="sm" variant="ghost" :class="{ 'bg-gray-200': editor.isActive('codeBlock') }"
          @click="editor.chain().focus().toggleCodeBlock().run()">
          <Icon icon="lucide:code" class="size-4" />
        </Button>
<!-- Text Color Icon Dropdown -->
<div class="relative">
  <Button variant="ghost" size="sm" @click="showColorDropdown = !showColorDropdown">
    <Icon icon="lucide:paint-bucket" class="size-4" />
  </Button>
  <div
    v-if="showColorDropdown"
    class="absolute z-50 mt-2 w-36 bg-white border rounded shadow p-2 grid grid-cols-4 gap-1"
  >
    <div
      v-for="color in textColors"
      :key="color"
      :style="{ backgroundColor: color }"
      class="h-6 w-6 rounded cursor-pointer border border-gray-200 hover:border-black"
      @click="() => {
        editor?.chain().focus().setColor(color).run()
        showColorDropdown = false
      }"
    ></div>
  </div>
</div>
<!-- Font Size Icon Dropdown -->
<div class="relative">
  <Button variant="ghost" size="sm" @click="showFontSizeDropdown = !showFontSizeDropdown">
    <Icon icon="lucide:text" class="size-4" />
  </Button>
  <div
    v-if="showFontSizeDropdown"
    class="absolute z-50 mt-2 w-28 bg-white border rounded shadow p-2"
  >
    <div
      v-for="size in fontSizes"
      :key="size"
      class="cursor-pointer px-2 py-1 hover:bg-gray-100 text-sm"
      @click="() => {
        editor?.chain().focus().setFontSize(size).run()
        showFontSizeDropdown = false
      }"
    >
      {{ size }}px
    </div>
  </div>
</div>

      

       
      </div>

      <EditorContent :editor="editor" class="p-3 min-h-[300px] outline-none prose max-w-[1200px] border-none" />
    </div>
  </div>
</div>

      </div>
      
      
      <div class="p-4 flex justify-end gap-2">
        <Button variant="outline" @click="emit('cancel-compose')">Cancel</Button>
<Button @click="submitIncident">Submit Report</Button>
      </div>
    </div>
    <div v-else-if="mail" class="flex flex-1 flex-col p-4">
       <div class="flex items-start p-5">
          <div class="font-bold text-xl">{{ mail.subject }}</div>
       </div>
           
              <Separator />
<div class="flex items-start p-4">
    <div class="flex items-start gap-4 text-sm">
      <Avatar>
     <AvatarFallback>
  {{ mail?.created_by?.name?.split(' ').map(n => n[0]).join('') || '?' }}
</AvatarFallback>
      </Avatar>
      <div class="grid gap-1">
        <div class="font-semibold">
      <div class="font-semibold">
  {{ mail?.created_by?.name ?? 'Unknown' }}
</div>
        </div>
        <div class="line-clamp-1 text-xs">
          {{ mail.created_by?.email ?? 'UNknown@gmail.com'}}
        </div>
      <div class="text-xs text-muted-foreground">
  Published: {{ mail?.occured_at ? format(new Date(mail.occured_at), "PPpp") : 'N/A' }}
</div>
      </div>
    </div>
  </div>
<div class="p-5 space-y-4">
<div v-if="mail?.involved_employees?.length" class="mt-4">
  <h3 class="font-medium">Involved Employees</h3>
  <div class="mt-2 flex flex-wrap gap-2">
    <Badge 
      v-for="employee in mail.involved_employees" 
      :key="employee.id"
      class="flex items-center gap-1"
    >
      <span>{{ employee.name }}</span>
      <span v-if="employee.email" class="text-xs opacity-75">({{ employee.email }})</span>
    </Badge>
  </div>
</div>
<div v-else class="mt-4">
  <h3 class="font-medium">Involved Employees</h3>
  <p class="text-muted-foreground text-sm">No employees involved</p>
</div>
      <div>
        <h3 class="font-medium">Date and Time of Incident</h3>
<p>{{ mail?.occured_at ? format(new Date(mail.occured_at), "PPpp") : 'N/A' }}</p>      </div>
      
      <div>
        <h3 class="font-medium">Location</h3>
        <p>{{ mail.location }}</p>
      </div>
      
      <div>
        <h3 class="font-medium">Type of Infraction</h3>
        <p>{{ mail.infraction_type }}</p>
      </div>
      
      <div>
        <h3 class="font-medium">Service Type</h3>
        <p>{{ mail.job_order?.serviceable_type || 'N/A' }}</p>
      </div>
      
      <div>
        <h3 class="font-medium">Job Order</h3>
        <p>{{ mail.job_order ? `JO-${mail.job_order.id}` : 'N/A' }}</p>
      </div>
      
      <div>
        <h3 class="font-medium">Status</h3>
        <Badge :variant="mail.status === 'resolved' ? 'default' : 'secondary'">
          {{ mail.status }}
        </Badge>
      </div>
    </div>
    
    
    <div class="prose max-w-none p-4" v-html="mail.html"></div>
        <div v-if="canVerify && mail.status === 'for verification'" class="p-4 flex justify-end gap-2">
      <Button 
        variant="default"
        @click="verifyIncident(mail.id)"
      >
        Verify Incident
      </Button>
    </div>
  </div>
    <div v-else class="p-8 text-center text-muted-foreground">
      No message selected
    </div>
  </div>
</template>
