import { AlertTriangle, CheckCircle, Clock, MapPin, Navigation, Users } from 'lucide-react'
import { Button } from '../components/Button'
import { Card, CardContent, CardHeader } from '../components/Card'
import { DashboardFrame, MetricGrid, Row } from './UserDashboard'

function VolunteerDashboard({ onNavigate }) {
  return (
    <DashboardFrame
      actions={<Button icon={<Navigation className="h-5 w-5" />} onClick={() => onNavigate?.('map')}>Open Map</Button>}
      eyebrow="Volunteer dashboard"
      title="Nearby rescue tasks and response status"
    >
      <MetricGrid
        items={[
          ['Open Calls', '8', AlertTriangle],
          ['Completed', '41', CheckCircle],
          ['Team Members', '16', Users],
        ]}
      />
      <div className="grid gap-6 lg:grid-cols-[1fr_0.8fr]">
        <Card>
          <CardHeader>
            <h2 className="text-2xl font-black text-slate-900">Priority Dispatch</h2>
            <p className="mt-2 text-slate-500">Frontend task cards ready for rescue assignment APIs.</p>
          </CardHeader>
          <CardContent className="grid gap-4">
            <Row icon={MapPin} title="Injured dog near Road 27" subtitle="Dhanmondi - high priority" />
            <Row icon={Clock} title="Mother cat with kittens" subtitle="Banani - pickup needed" />
            <Row icon={AlertTriangle} title="Lost puppy near school" subtitle="Uttara - awaiting volunteer" />
          </CardContent>
        </Card>
        <Card className="bg-white">
          <CardHeader>
            <h2 className="text-2xl font-black text-slate-900">Shift Availability</h2>
            <p className="mt-2 text-slate-500">Toggle slots later when backend scheduling is connected.</p>
          </CardHeader>
          <CardContent className="grid gap-3">
            {['Morning', 'Afternoon', 'Evening'].map((slot) => (
              <label className="flex items-center justify-between rounded-2xl bg-orange-50 px-4 py-3 font-bold text-slate-700" key={slot}>
                <span>{slot}</span>
                <input className="h-5 w-5 accent-orange-500" type="checkbox" />
              </label>
            ))}
          </CardContent>
        </Card>
      </div>
    </DashboardFrame>
  )
}

export default VolunteerDashboard
