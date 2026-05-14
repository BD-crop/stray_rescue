import { Calendar, Heart, HomeIcon, Package, Stethoscope, Users } from 'lucide-react'
import { Button } from '../components/Button'
import { Card, CardContent, CardHeader } from '../components/Card'
import { DashboardFrame, MetricGrid, Row } from './UserDashboard'

function ShelterDashboard({ onNavigate }) {
  return (
    <DashboardFrame
      actions={<Button onClick={() => onNavigate?.('report')}>Add Intake</Button>}
      eyebrow="Shelter dashboard"
      title="Manage intake, care, and adoption readiness"
    >
      <MetricGrid
        items={[
          ['Animals in Care', '58', HomeIcon],
          ['Pending Adoptions', '14', Heart],
          ['Care Tasks', '23', Stethoscope],
        ]}
      />
      <div className="grid gap-6 lg:grid-cols-[1fr_0.8fr]">
        <Card>
          <CardHeader>
            <h2 className="text-2xl font-black text-slate-900">Care Queue</h2>
            <p className="mt-2 text-slate-500">Operational shelter cards for future CRUD endpoints.</p>
          </CardHeader>
          <CardContent className="grid gap-4">
            <Row icon={Stethoscope} title="Vaccination round" subtitle="12 animals scheduled today" />
            <Row icon={Calendar} title="Adoption interviews" subtitle="5 families booked" />
            <Row icon={Package} title="Food stock review" subtitle="Dry food below weekly target" />
          </CardContent>
        </Card>
        <Card className="bg-orange-50/70">
          <CardHeader>
            <Users className="mb-4 h-8 w-8 text-orange-500" />
            <h2 className="text-2xl font-black text-slate-900">Volunteer Coordination</h2>
            <p className="mt-2 text-slate-500">Assign transport, foster, and feeding work from one view.</p>
          </CardHeader>
          <CardContent>
            <Button className="w-full" onClick={() => onNavigate?.('community')} variant="outline">
              Message Community
            </Button>
          </CardContent>
        </Card>
      </div>
    </DashboardFrame>
  )
}

export default ShelterDashboard
