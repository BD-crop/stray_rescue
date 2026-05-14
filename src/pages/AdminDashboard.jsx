import { Activity, Building2, ChartColumn, ShieldCheck, Siren, Users } from 'lucide-react'
import { Button } from '../components/Button'
import { Card, CardContent, CardHeader } from '../components/Card'
import { DashboardFrame, MetricGrid, Row } from './UserDashboard'

function AdminDashboard({ onNavigate }) {
  return (
    <DashboardFrame
      actions={<Button onClick={() => onNavigate?.('map')}>Monitor Network</Button>}
      eyebrow="Admin dashboard"
      title="Platform overview for rescue operations"
    >
      <MetricGrid
        items={[
          ['Active Users', '8.4k', Users],
          ['Shelters', '156', Building2],
          ['Open Incidents', '37', Siren],
        ]}
      />
      <div className="grid gap-6 lg:grid-cols-[1fr_0.8fr]">
        <Card>
          <CardHeader>
            <h2 className="text-2xl font-black text-slate-900">Network Health</h2>
            <p className="mt-2 text-slate-500">Admin widgets styled for analytics and moderation APIs.</p>
          </CardHeader>
          <CardContent className="grid gap-4">
            <Row icon={Activity} title="Response time improving" subtitle="Average dispatch down 18%" />
            <Row icon={ShieldCheck} title="Verification queue" subtitle="9 shelter documents pending" />
            <Row icon={ChartColumn} title="Adoptions rising" subtitle="Monthly adoption rate up 12%" />
          </CardContent>
        </Card>
        <Card className="bg-slate-950 text-white">
          <CardHeader>
            <h2 className="text-2xl font-black">Admin Controls</h2>
            <p className="mt-2 text-slate-300">Frontend controls for user roles, reports, and shelter approval flows.</p>
          </CardHeader>
          <CardContent className="grid gap-3">
            {['Review reports', 'Approve shelters', 'Manage volunteers'].map((item) => (
              <button className="rounded-2xl bg-white/8 px-4 py-3 text-left font-bold transition-colors hover:bg-white/12" key={item} type="button">
                {item}
              </button>
            ))}
          </CardContent>
        </Card>
      </div>
    </DashboardFrame>
  )
}

export default AdminDashboard
