export const safetyInspectionChecklist = [
  {
    id: 'isVehicleInspectionFilled',
    description: 'Vehicle Inspection Form',
  },
  {
    id: 'isUniformPpeFilled',
    description: 'Uniform and PPE Request Form',
  },
  {
    id: 'isToolsEquipmentFilled',
    description: 'Tools and Equipment Request Form',
  },
  {
    id: 'isCertified',
    description:
      'I hereby certify on my honor to the accuracy of the foregoing information of safety inspection checklist.',
  },
] as const

export type ChecklistItemType = (typeof safetyInspectionChecklist)[number]['id']
