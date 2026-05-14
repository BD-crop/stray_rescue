import { Calendar, Heart, MapPin, MessageCircle, ShieldCheck } from 'lucide-react'
import { Button } from '../components/Button'
import { Card, CardContent, CardHeader } from '../components/Card'

function UserDashboard({ onNavigate }) {
  return (
    <DashboardFrame
      eyebrow="Animal lover dashboard"
      title="Your adoption and rescue activity"
      actions={<Button onClick={() => onNavigate?.('adopt')}>Browse Pets</Button>}
    >
      <MetricGrid
        items={[
          ['Saved Pets', '12', Heart],
          ['Adoption Requests', '3', Calendar],
          ['Reports Submitted', '7', MapPin],
        ]}
      />
      <div className="grid gap-6 lg:grid-cols-[1fr_0.8fr]">
        <Card>
          <CardHeader>
            <h2 className="text-2xl font-black text-slate-900">Recent Matches</h2>
            <p className="mt-2 text-slate-500">Pets that match your saved preferences.</p>
          </CardHeader>
          <CardContent className="grid gap-4">
            {['Luna - Local Mix', 'Milo - Rescue Cat', 'Bruno - Street Puppy'].map((item) => (
              <Row icon={Heart} key={item} title={item} subtitle="Ready for adoption review" />
            ))}
          </CardContent>
        </Card>
        <Card className="bg-orange-50/70">
          <CardHeader>
            <ShieldCheck className="mb-4 h-8 w-8 text-orange-500" />
            <h2 className="text-2xl font-black text-slate-900">Profile Strength</h2>
            <p className="mt-2 text-slate-500">Complete contact and home details to speed up shelter approval.</p>
          </CardHeader>
          <CardContent>
            <div className="mb-4 h-3 overflow-hidden rounded-full bg-white">
              <div className="h-full w-3/4 rounded-full bg-orange-500" />
            </div>
            <Button className="w-full" variant="outline">Update Profile</Button>
          </CardContent>
        </Card>
      </div>
    </DashboardFrame>
  )
}

function DashboardFrame({ eyebrow, title, actions, children }) {
  return (
    <div className="min-h-screen bg-[#fffaf3] px-4 py-10 sm:px-6 lg:px-8">
      <div className="mx-auto max-w-7xl">
        <div className="mb-8 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
          <div>
            <div className="mb-3 inline-flex rounded-full bg-orange-100 px-4 py-2 text-xs font-black uppercase tracking-[0.22em] text-orange-600">
              {eyebrow}
            </div>
            <h1 className="text-4xl font-black tracking-tight text-slate-900 md:text-5xl">{title}</h1>
          </div>
          {actions}
        </div>
        <div className="grid gap-6">{children}</div>
      </div>
    </div>
  )
}

function MetricGrid({ items }) {
  return (
    <div className="grid gap-5 md:grid-cols-3">
      {items.map(([label, value, Icon]) => (
        <Card className="bg-white/95" key={label}>
          <CardHeader>
            <div className="mb-5 grid h-12 w-12 place-items-center rounded-2xl bg-orange-100 text-orange-500">
              <Icon className="h-6 w-6" />
            </div>
            <div className="text-4xl font-black text-slate-900">{value}</div>
            <div className="mt-1 font-bold text-slate-500">{label}</div>
          </CardHeader>
        </Card>
      ))}
    </div>
  )
}

function Row({ icon: Icon, title, subtitle }) {
  return (
    <div className="flex items-center gap-4 rounded-2xl border border-slate-100 bg-slate-50 p-4">
      <div className="grid h-11 w-11 place-items-center rounded-2xl bg-white text-orange-500">
        <Icon className="h-5 w-5" />
      </div>
      <div className="min-w-0 flex-1">
        <div className="font-black text-slate-900">{title}</div>
        <div className="text-sm font-semibold text-slate-500">{subtitle}</div>
      </div>
      <MessageCircle className="h-5 w-5 text-slate-300" />
    </div>
  )
}

export { DashboardFrame, MetricGrid, Row }
export default UserDashboard
